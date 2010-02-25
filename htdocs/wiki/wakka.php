<?php
/* encoding: iso-8859-1
wakka.php
Copyright (c) 2002, Hendrik Mans <hendrik@mans.de>
Copyright  2003 Carlo Zottmann
Copyright 2002, 2003 David DELON
Copyright 2002, 2003, 2004 Charles NÉPOTE
Copyright 2002, 2003 Patrick PAUL
Copyright 2003 Éric DELORD
Copyright 2003 Éric FELDSTEIN
Copyright 2004 Jean-Christophe ANDRÉ
All rights reserved.
Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions
are met:
1. Redistributions of source code must retain the above copyright
notice, this list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright
notice, this list of conditions and the following disclaimer in the
documentation and/or other materials provided with the distribution.
3. The name of the author may not be used to endorse or promote products
derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,
INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

/*
    Yes, most of the formatting used in this file is HORRIBLY BAD STYLE. However,
    most of the action happens outside of this file, and I really wanted the code
    to look as small as what it does. Basically. Oh, I just suck. :)
*/



// do not change this line, you fool. In fact, don't change anything! Ever!
define("WAKKA_VERSION", "0.1.1");
define("WIKINI_VERSION", "0.4.3");
// start the compute time
list($g_usec, $g_sec) = explode(" ",microtime());
define ("t_start", (float)$g_usec + (float)$g_sec);
$t_SQL=0;



class Wiki
{
	var $dblink;
	var $page;
	var $tag;
	var $parameter = array();
	var $queryLog = array();
	var $interWiki = array();
	var $VERSION;
	var $CookiePath = '/';


	// constructor
	function Wiki($config)
	{
		$this->config = $config;
		// some host do not allow mysql_pconnect
		$this->dblink = @mysql_connect (
			$this->config["mysql_host"],
			$this->config["mysql_user"],
			$this->config["mysql_password"]);
		if ($this->dblink)
		{
			if (!@mysql_select_db($this->config["mysql_database"], $this->dblink))
			{
				@mysql_close($this->dblink);
				$this->dblink = false;
			}
		}
		$this->VERSION = WAKKA_VERSION;

		//determine le chemin pour le cookie
		$a = parse_url($this->GetConfigValue('base_url'));
		$this->CookiePath = dirname($a['path']);
		if ($this->CookiePath != '/') $this->CookiePath .= '/';
	}



	// DATABASE
	function Query($query)
	{
		if($this->GetConfigValue("debug")) $start = $this->GetMicroTime();
		if (!$result = mysql_query($query, $this->dblink))
		{
			ob_end_clean();
			die("Query failed: ".$query." (".mysql_error().")");
		}
		if($this->GetConfigValue("debug"))
		{
			$time = $this->GetMicroTime() - $start;
			$this->queryLog[] = array(
				"query"		=> $query,
				"time"		=> $time);
		}
		return $result;
	}
	function LoadSingle($query) { if ($data = $this->LoadAll($query)) return $data[0]; }
	function LoadAll($query)
	{
	$data=array();
	if ($r = $this->Query($query))
		{
			while ($row = mysql_fetch_assoc($r)) $data[] = $row;
			mysql_free_result($r);
		}
		return $data;
	}



	// MISC
	function GetMicroTime() { list($usec, $sec) = explode(" ",microtime()); return ((float)$usec + (float)$sec); }
	function IncludeBuffered($filename, $notfoundText = "", $vars = "", $path = "")
	{
		if ($path) $dirs = explode(":", $path);
		else $dirs = array("");

		foreach($dirs as $dir)
		{
			if ($dir) $dir .= "/";
			$fullfilename = $dir.$filename;
			if (file_exists($fullfilename))
			{
				if (is_array($vars)) extract($vars);

				ob_start();
				include($fullfilename);
				$output = ob_get_contents();
				ob_end_clean();
				return $output;
			}
		}
		if ($notfoundText) return $notfoundText;
		else return false;
	}



	// VARIABLES
	function GetPageTag() { return $this->tag; }
	function GetPageTime() { return $this->page["time"]; }
	function GetMethod() { return $this->method; }
	function GetConfigValue($name) { return $this->config[$name]; }
	function GetWakkaName() { return $this->GetConfigValue("wakka_name"); }
	function GetWakkaVersion() { return $this->VERSION; }
	function GetWikiNiVersion() { return WIKINI_VERSION; } 



	// PAGES
	function LoadPage($tag, $time = "", $cache = 1) {
		// retrieve from cache
		if (!$time && $cache && ($cachedPage = $this->GetCachedPage($tag))) { $page = $cachedPage;}
		// load page
		if (!isset($page)) $page = $this->LoadSingle("select * from ".$this->config["table_prefix"]."pages where tag = '".mysql_escape_string($tag)."' ".($time ? "and time = '".mysql_escape_string($time)."'" : "and latest = 'Y'")." limit 1");
		// cache result
		if (!$time) $this->CachePage($page);
		return $page;
	}
	function GetCachedPage($tag) {return (isset($this->pageCache[$tag]) ? $this->pageCache[$tag] : ''); }
	function CachePage($page) { $this->pageCache[$page["tag"]] = $page; }
	function SetPage($page) { $this->page = $page; if ($this->page["tag"]) $this->tag = $this->page["tag"]; }
	function LoadPageById($id) { return $this->LoadSingle("select * from ".$this->config["table_prefix"]."pages where id = '".mysql_escape_string($id)."' limit 1"); }
	function LoadRevisions($page) { return $this->LoadAll("select * from ".$this->config["table_prefix"]."pages where tag = '".mysql_escape_string($page)."' order by time desc"); }
	function LoadPagesLinkingTo($tag) { return $this->LoadAll("select from_tag as tag from ".$this->config["table_prefix"]."links where to_tag = '".mysql_escape_string($tag)."' order by tag"); }
	function LoadRecentlyChanged($limit=50) {
		$limit= (int) $limit;
		if ($pages = $this->LoadAll("select tag, time, user, owner from ".$this->config["table_prefix"]."pages where latest = 'Y' and comment_on = '' order by time desc limit $limit"))
		{
			foreach ($pages as $page)
			{
				$this->CachePage($page);
			}
			return $pages;
		}
	}
	function LoadAllPages() { return $this->LoadAll("select * from ".$this->config["table_prefix"]."pages where latest = 'Y' order by tag"); }
	function FullTextSearch($phrase) { return $this->LoadAll("select * from ".$this->config["table_prefix"]."pages where latest = 'Y' and match(tag, body) against('".mysql_escape_string($phrase)."')"); }
	function LoadWantedPages() { return $this->LoadAll("select distinct ".$this->config["table_prefix"]."links.to_tag as tag,count(".$this->config["table_prefix"]."links.from_tag) as count from ".$this->config["table_prefix"]."links left join ".$this->config["table_prefix"]."pages on ".$this->config["table_prefix"]."links.to_tag = ".$this->config["table_prefix"]."pages.tag where ".$this->config["table_prefix"]."pages.tag is NULL group by tag order by count desc"); }
	function LoadOrphanedPages() { return $this->LoadAll("select distinct tag from ".$this->config["table_prefix"]."pages left join ".$this->config["table_prefix"]."links on ".$this->config["table_prefix"]."pages.tag = ".$this->config["table_prefix"]."links.to_tag where ".$this->config["table_prefix"]."links.to_tag is NULL and ".$this->config["table_prefix"]."pages.comment_on = '' order by tag"); }
	function IsOrphanedPage($tag) { return $this->LoadAll("select distinct tag from ".$this->config["table_prefix"]."pages left join ".$this->config["table_prefix"]."links on ".$this->config["table_prefix"]."pages.tag = ".$this->config["table_prefix"]."links.to_tag where ".$this->config["table_prefix"]."links.to_tag is NULL and ".$this->config["table_prefix"]."pages.comment_on ='' and tag='".mysql_escape_string($tag)."'"); }
	function DeleteOrphanedPage($tag) {
		$this->Query("delete from ".$this->config["table_prefix"]."pages where tag='".mysql_escape_string($tag)."' ");
		$this->Query("delete from ".$this->config["table_prefix"]."links where from_tag='".mysql_escape_string($tag)."' ");
		$this->Query("delete from ".$this->config["table_prefix"]."acls where page_tag='".mysql_escape_string($tag)."' ");
		$this->Query("delete from ".$this->config["table_prefix"]."referrers where page_tag='".mysql_escape_string($tag)."' ");
	}
	function SavePage($tag, $body, $comment_on = "") {
		// get current user
		$user = $this->GetUserName();

		//die($tag);

		// TODO: check write privilege
		if ($this->HasAccess("write", $tag))
		{
			// is page new?
			if (!$oldPage = $this->LoadPage($tag))
			{
				// create default write acl. store empty write ACL for comments.
				$this->SaveAcl($tag, "write", ($comment_on ? "" : $this->GetConfigValue("default_write_acl")));

				// create default read acl
				$this->SaveAcl($tag, "read", $this->GetConfigValue("default_read_acl"));

				// create default comment acl.
				$this->SaveAcl($tag, "comment", $this->GetConfigValue("default_comment_acl"));

				// current user is owner; if user is logged in! otherwise, no owner.
				if ($this->GetUser()) $owner = $user;
			}
			else
			{
				// aha! page isn't new. keep owner!
				$owner = $oldPage["owner"];
			}


			// set all other revisions to old
			$this->Query("update ".$this->config["table_prefix"]."pages set latest = 'N' where tag = '".mysql_Escape_string($tag)."'");

			// add new revision
			$this->Query("insert into ".$this->config["table_prefix"]."pages set ".
				"tag = '".mysql_escape_string($tag)."', ".
				($comment_on ? "comment_on = '".mysql_escape_string($comment_on)."', " : "").
				"time = now(), ".
				"owner = '".mysql_escape_string($owner)."', ".
				"user = '".mysql_escape_string($user)."', ".
				"latest = 'Y', ".
				"body = '".mysql_escape_string(chop($body))."'");
		}
	}
	function PurgePages() {
		if ($days = $this->GetConfigValue("pages_purge_time")) {
			// Selection of pages which can be deleted 
			$pages = $this->LoadAll("select distinct tag, time from ".$this->config["table_prefix"]."pages where time < date_sub(now(), interval '".mysql_escape_string($days)."' day) and latest = 'N' order by time asc");
			foreach ($pages as $page) {
				// Deletion if there are more than 2 versions avalaible (TODO : parameter ?)
				$tags=$this->LoadAll("select distinct tag from ".$this->config["table_prefix"]."pages where tag = '".mysql_escape_string($page[tag])."' group by tag having count(*) > 2 order by tag");
				foreach ($tags as $tag) {
					$this->Query("delete from ".$this->config["table_prefix"]."pages where time = '".mysql_escape_string($page[time])."' and tag = '".mysql_escape_string($tag[tag])."'");
				}
			}
		}
	}



	// COOKIES
	function SetSessionCookie($name, $value) { SetCookie($name, $value, 0, $this->CookiePath); $_COOKIE[$name] = $value; }
	function SetPersistentCookie($name, $value, $remember = 0) { SetCookie($name, $value, time() + ($remember ? 90*24*60*60 : 60 * 60), $this->CookiePath); $_COOKIE[$name] = $value; }
	function DeleteCookie($name) { SetCookie($name, "", 1, $this->CookiePath); $_COOKIE[$name] = ""; }
	function GetCookie($name) { return $_COOKIE[$name]; }



	// HTTP/REQUEST/LINK RELATED
	function SetMessage($message) { $_SESSION["message"] = $message; }
	function GetMessage()
	{
		if (isset($_SESSION["message"])) $message = $_SESSION["message"];
		else $message = "";
		$_SESSION["message"] = "";
		return $message;
	}
	function Redirect($url)
	{
		header("Location: $url");
		exit;
	}
	// returns just PageName[/method].
	function MiniHref($method = "", $tag = "")
	{
		if (!$tag = trim($tag)) $tag = $this->tag;
		return $tag.($method ? "/".$method : "");
	}
	// returns the full url to a page/method.
	function Href($method = "", $tag = "", $params = "")
	{
		$href = $this->config["base_url"].$this->MiniHref($method, $tag);
		if ($params)
		{
			$href .= ($this->config["rewrite_mode"] ? "?" : "&amp;").$params;
		}
		return $href;
	}
	function Link($tag, $method = "", $text = "", $track = 1) {
		$tag=htmlspecialchars($tag); //avoid xss
		$text=htmlspecialchars($text); //paranoiac again
		if (!$text) $text = $tag;

		// is this an interwiki link?
		if (preg_match("/^([A-Z][A-Z,a-z]+)[:]([A-Z,a-z,0-9]*)$/s", $tag, $matches))
		{
			$tag = $this->GetInterWikiUrl($matches[1], $matches[2]);
			return "<a href=\"$tag\">$text (interwiki)</a>";
		}
		// is this a full link? ie, does it contain non alpha-numeric characters?
		// Note : [:alnum:] is equivalent [0-9A-Za-z]
		//        [^[:alnum:]] means : some caracters other than [0-9A-Za-z]
		// For example : "www.adress.com", "mailto:adress@domain.com", "http://www.adress.com"
		else if (preg_match("/[^[:alnum:]]/", $tag))
		{
			// check for email addresses
			if (preg_match("/^.+\@.+$/", $tag))
			{
				$tag = "mailto:".$tag;
			}
			// check for protocol-less URLs
			else if (!preg_match("/:\/\//", $tag))
			{
				$tag = "http://".$tag;	//Very important for xss (avoid javascript:() hacking)
			}
			// is this an inline image (text!=tag and url ends png,gif,jpeg)
			if ($text!=$tag and preg_match("/.(gif|jpeg|png|jpg)$/i",$tag))
			{
				return "<img src=\"$tag\" alt=\"$text\" />";
			}
			else
			{
				return "<a href=\"$tag\">$text</a>";
			}
		}
		else
		{
			// it's a Wiki link!
			if (isset($_SESSION["linktracking"]) && $track) $this->TrackLinkTo($tag);
			return ($this->LoadPage($tag) ? "<a href=\"".$this->href($method, $tag)."\">".$text."</a>" : "<span class=\"missingpage\">".$text."</span><a href=\"".$this->href("edit", $tag)."\">?</a>");
		}
	}
	function ComposeLinkToPage($tag, $method = "", $text = "", $track = 1) {
		if (!$text) $text = $tag;
		$text = htmlentities($text);
		if (isset($_SESSION["linktracking"]) && $track)
			$this->TrackLinkTo($tag);
		return '<a href="'.$this->href($method, $tag).'">'.$text.'</a>';
	}
	// function PregPageLink($matches) { return $this->Link($matches[1]); }
	function IsWikiName($text) { return preg_match("/^[A-Z][a-z]+[A-Z,0-9][A-Z,a-z,0-9]*$/", $text); }
	function TrackLinkTo($tag) { $_SESSION["linktable"][] = $tag; }
	function GetLinkTable() { return $_SESSION["linktable"]; }
	function ClearLinkTable() { $_SESSION["linktable"] = array(); }
	function StartLinkTracking() { $_SESSION["linktracking"] = 1; }
	function StopLinkTracking() { $_SESSION["linktracking"] = 0; }
	function WriteLinkTable() {
		// delete old link table
		$this->Query("delete from ".$this->config["table_prefix"]."links where from_tag = '".mysql_escape_string($this->GetPageTag())."'");
		if ($linktable = $this->GetLinkTable())
		{
			$from_tag = mysql_escape_string($this->GetPageTag());
			foreach ($linktable as $to_tag)
			{
				$lower_to_tag = strtolower($to_tag);
				if (!$written[$lower_to_tag])
				{
					$this->Query("insert into ".$this->config["table_prefix"]."links set from_tag = '".$from_tag."', to_tag = '".mysql_escape_string($to_tag)."'");
					$written[$lower_to_tag] = 1;
				}
			}
		}
	}
	function Header() { return $this->Action($this->GetConfigValue("header_action"), 1); }
	function Footer() { return $this->Action($this->GetConfigValue("footer_action"), 1); }



	// FORMS
	function FormOpen($method = "", $tag = "", $formMethod = "post") {
		$result = "<form action=\"".$this->href($method, $tag)."\" method=\"".$formMethod."\">\n";
		if (!$this->config["rewrite_mode"]) $result .= "<input type=\"hidden\" name=\"wiki\" value=\"".$this->MiniHref($method, $tag)."\" />\n";
		return $result;
	}
	function FormClose() {
		return "</form>\n";
	}



	// INTERWIKI STUFF
	function ReadInterWikiConfig() {
		if ($lines = file("interwiki.conf"))
		{
			foreach ($lines as $line)
			{
				if ($line = trim($line))
				{
					list($wikiName, $wikiUrl) = explode(" ", trim($line));
					$this->AddInterWiki($wikiName, $wikiUrl);
				}
			}
		}
	}
	function AddInterWiki($name, $url) {
		$this->interWiki[$name] = $url;
	}
	function GetInterWikiUrl($name, $tag) {
		if (isset($this->interWiki[$name]))
		{
			return $this->interWiki[$name].$tag;
		} else {
		return 'http://'.$tag; //avoid xss by putting http:// in front of JavaScript:()
		}
	}



	// REFERRERS
	function LogReferrer($tag = "", $referrer = "") {
		// fill values
		if (!$tag = trim($tag)) $tag = $this->GetPageTag();
		if (!$referrer = trim($referrer) AND isset($_SERVER["HTTP_REFERER"])) $referrer = $_SERVER["HTTP_REFERER"];
		
		// check if it's coming from another site
		if ($referrer && !preg_match("/^".preg_quote($this->GetConfigValue("base_url"), "/")."/", $referrer))
		{
			$this->Query("insert into ".$this->config["table_prefix"]."referrers set ".
				"page_tag = '".mysql_escape_string($tag)."', ".
				"referrer = '".mysql_escape_string($referrer)."', ".
				"time = now()");
		}
	}
	function LoadReferrers($tag = "") {
		return $this->LoadAll("select referrer, count(referrer) as num from ".$this->config["table_prefix"]."referrers ".($tag = trim($tag) ? "where page_tag = '".mysql_escape_string($tag)."'" : "")." group by referrer order by num desc");
	}
	function PurgeReferrers() {
		if ($days = $this->GetConfigValue("referrers_purge_time")) {
			$this->Query("delete from ".$this->config["table_prefix"]."referrers where time < date_sub(now(), interval '".mysql_escape_string($days)."' day)");
		}
	}



	// PLUGINS
	function Action($action, $forceLinkTracking = 0)
	{
		$action = trim($action); $vars=array();
		// stupid attributes check
		if ((stristr($action, "=\"")) || (stristr($action, "/")))
		{
			// extract $action and $vars_temp ("raw" attributes)
			preg_match("/^([A-Za-z0-9]*)\/?(.*)$/", $action, $matches);
			list(, $action, $vars_temp) = $matches;
			// match all attributes (key and value)
			$this->parameter[$vars_temp]=$vars_temp;
			preg_match_all("/([A-Za-z0-9]*)=\"(.*)\"/U", $vars_temp, $matches);

		// prepare an array for extract() to work with (in $this->IncludeBuffered())
		if (is_array($matches))
			{
				for ($a = 0; $a < count($matches[1]); $a++)
				{
					$vars[$matches[1][$a]] = $matches[2][$a];
					$this->parameter[$matches[1][$a]]=$matches[2][$a];
				}
			}
		}
		if (!$forceLinkTracking) $this->StopLinkTracking();
		$result = $this->IncludeBuffered(strtolower($action).".php", "<i>Action inconnue \"$action\"</i>", $vars, $this->config["action_path"]);
		$this->StartLinkTracking();
		if (isset($parameter)) unset($this->parameter[$parameter]);
		unset($this->parameter);
		return $result;
	}
	function Method($method) {
		if (!$handler = $this->page["handler"]) $handler = "page";
		$methodLocation = $handler."/".$method.".php";
		return $this->IncludeBuffered($methodLocation, "<i>M&eacute;thode inconnue \"$methodLocation\"</i>", "", $this->config["handler_path"]);
	}
	function Format($text, $formatter = "wakka") {
		return $this->IncludeBuffered("formatters/".$formatter.".php", "<i>Impossible de trouver le formateur \"$formatter\"</i>", compact("text")); 
	}



	// USERS
	function LoadUser($name, $password = 0) { return $this->LoadSingle("select * from ".$this->config["table_prefix"]."users where name = '".mysql_escape_string($name)."' ".($password === 0 ? "" : "and password = '".mysql_escape_string($password)."'")." limit 1"); }
	function LoadUsers() { return $this->LoadAll("select * from ".$this->config["table_prefix"]."users order by name"); }
	function GetUserName() { if ($user = $this->GetUser()) $name = $user["name"]; else if (!$name = gethostbyaddr($_SERVER["REMOTE_ADDR"])) $name = $_SERVER["REMOTE_ADDR"]; return $name; }
	function UserName() { /* deprecated! */ return $this->GetUserName(); }
	function GetUser() { return (isset($_SESSION["user"]) ? $_SESSION["user"] : '');}
	function SetUser($user, $remember=0) { $_SESSION["user"] = $user; $this->SetPersistentCookie("name", $user["name"], $remember); $this->SetPersistentCookie("password", $user["password"], $remember); $this->SetPersistentCookie("remember", $remember, $remember); }
	function LogoutUser() { $_SESSION["user"] = ""; $this->DeleteCookie("name"); $this->DeleteCookie("password"); }
	function UserWantsComments() { if (!$user = $this->GetUser()) return false; return ($user["show_comments"] == "Y"); }
	function GetParameter($parameter, $default = '') { return (isset($this->parameter[$parameter]) ? $this->parameter[$parameter] : $default); }


	
	// COMMENTS
	function LoadComments($tag) { return $this->LoadAll("select * from ".$this->config["table_prefix"]."pages where comment_on = '".mysql_escape_string($tag)."' and latest = 'Y' order by time"); }
	function LoadRecentComments() { return $this->LoadAll("select * from ".$this->config["table_prefix"]."pages where comment_on != '' and latest = 'Y' order by time desc"); }
	function LoadRecentlyCommented($limit = 50) {
		// NOTE: this is really stupid. Maybe my SQL-Fu is too weak, but apparently there is no easier way to simply select
		//       all comment pages sorted by their first revision's (!) time. ugh!
		
		// load ids of the first revisions of latest comments. err, huh?
		$pages=array();
		$comments=array();
		if ($ids = $this->LoadAll("select min(id) as id from ".$this->config["table_prefix"]."pages where comment_on != '' group by tag order by id desc"))
		{
			// load complete comments
			$num=0;
			foreach ($ids as $id)
			{
				$comment = $this->LoadSingle("select * from ".$this->config["table_prefix"]."pages where id = '".$id["id"]."' limit 1");
				if (!isset($comments[$comment["comment_on"]]) && $num < $limit)
				{
					$comments[$comment["comment_on"]] = $comment;
					$num++;
				}
			}
		
			// now load pages
			if ($comments)
			{
				// now using these ids, load the actual pages
				foreach ($comments as $comment)
				{
					$page = $this->LoadPage($comment["comment_on"]);
					$page["comment_user"] = $comment["user"];
					$page["comment_time"] = $comment["time"];
					$page["comment_tag"] = $comment["tag"];
					$pages[] = $page;
				}
			}
		}
		// load tags of pages 
		//return $this->LoadAll("select comment_on as tag, max(time) as time, tag as comment_tag, user from ".$this->config["table_prefix"]."pages where comment_on != '' group by comment_on order by time desc");
		return $pages;
	}



	// ACCESS CONTROL
	// returns true if logged in user is owner of current page, or page specified in $tag
	function UserIsOwner($tag = "") {
		// check if user is logged in
		if (!$this->GetUser()) return false;

		// set default tag
		if (!$tag = trim($tag)) $tag = $this->GetPageTag();
		
		// check if user is owner
		if ($this->GetPageOwner($tag) == $this->GetUserName()) return true;
	}
	function GetPageOwner($tag = "", $time = "") { if (!$tag = trim($tag)) $tag = $this->GetPageTag(); if ($page = $this->LoadPage($tag, $time)) return $page["owner"]; }
	function SetPageOwner($tag, $user) {
		// check if user exists
		if (!$this->LoadUser($user)) return;
		
		// updated latest revision with new owner
		$this->Query("update ".$this->config["table_prefix"]."pages set owner = '".mysql_escape_string($user)."' where tag = '".mysql_escape_string($tag)."' and latest = 'Y' limit 1");
	}
	function LoadAcl($tag, $privilege, $useDefaults = 1) {
		if ((!$acl = $this->LoadSingle("select * from ".$this->config["table_prefix"]."acls where page_tag = '".mysql_escape_string($tag)."' and privilege = '".mysql_escape_string($privilege)."' limit 1")) && $useDefaults)
		{
			$acl = array("page_tag" => $tag, "privilege" => $privilege, "list" => $this->GetConfigValue("default_".$privilege."_acl"));
		}
		return $acl;
	}
	function SaveAcl($tag, $privilege, $list) {
		if ($this->LoadAcl($tag, $privilege, 0)) $this->Query("update ".$this->config["table_prefix"]."acls set list = '".mysql_escape_string(trim(str_replace("\r", "", $list)))."' where page_tag = '".mysql_escape_string($tag)."' and privilege = '".mysql_escape_string($privilege)."' limit 1");
		else $this->Query("insert into ".$this->config["table_prefix"]."acls set list = '".mysql_escape_string(trim(str_replace("\r", "", $list)))."', page_tag = '".mysql_escape_string($tag)."', privilege = '".mysql_escape_string($privilege)."'");
	}
	// returns true if $user (defaults to current user) has access to $privilege on $page_tag (defaults to current page)
	function HasAccess($privilege, $tag = "", $user = "") {
		// set defaults
		if (!$tag = trim($tag)) $tag = $this->GetPageTag();
		if (!$user = $this->GetUserName());
		
		// load acl
		$acl = $this->LoadAcl($tag, $privilege);
		
		// if current user is owner, return true. owner can do anything!
		if ($this->UserIsOwner($tag)) return true;
		
		// fine fine... now go through acl
		foreach (explode("\n", $acl["list"]) as $line)
		{
			$line = trim($line);

			// check for inversion character "!"
			if (preg_match("/^[!](.*)$/", $line, $matches))
			{
				$negate = 1;
				$line = $matches[1];
			}
			else
			{
				$negate = 0;
			}

			// if there's still anything left... lines with just a "!" don't count!
			if ($line)
			{
				switch ($line[0])
				{
				// comments
				case "#":
					break;
				// everyone
				case "*":
					return !$negate;
				// aha! a user entry.
				case "+":
					if (!$this->LoadUser($user)) 
					{
						return $negate;
					}
					else
					{
						return !$negate;
					}
				default:
					if ($line == $user)
					{
						return !$negate;
					}
				}
			}
		}
		
		// tough luck.
		return false;
	}



	// MAINTENANCE
	function Maintenance() {
		// purge referrers
		$this->PurgeReferrers();
		// purge old page revisions
		$this->PurgePages();
	}



	// THE BIG EVIL NASTY ONE!
	function Run($tag, $method = "") {
		if(!($this->GetMicroTime()%3)) $this->Maintenance(); 

		$this->ReadInterWikiConfig();

		// do our stuff!
		if (!$this->method = trim($method)) $this->method = "show";
		if (!$this->tag = trim($tag)) $this->Redirect($this->href("", $this->config["root_page"]));
		if ((!$this->GetUser() && isset($_COOKIE["name"])) && ($user = $this->LoadUser($_COOKIE["name"], $_COOKIE["password"]))) $this->SetUser($user, $_COOKIE["remember"]);
		$this->SetPage($this->LoadPage($tag, (isset($_REQUEST["time"]) ? $_REQUEST["time"] :'')));
		$this->LogReferrer();

      //correction pour un support plus facile de nouveaux handlers
      print($this->Method($this->method));
	}
}



// stupid version check
if (!isset($_REQUEST)) die('$_REQUEST[] not found. Wakka requires PHP 4.1.0 or higher!');

// workaround for the amazingly annoying magic quotes.
function magicQuotesSuck(&$a)
{
	if (is_array($a))
	{
		foreach ($a as $k => $v)
		{
			if (is_array($v))
				magicQuotesSuck($a[$k]);
			else
				$a[$k] = stripslashes($v);
		}
	}
}
set_magic_quotes_runtime(0);
if (get_magic_quotes_gpc())
{
	magicQuotesSuck($_POST);
	magicQuotesSuck($_GET);
	magicQuotesSuck($_COOKIE);
}


// default configuration values
$wakkaConfig= array();
$wakkaDefaultConfig = array(
	'wakka_version'		=> '',
	'wikini_version'	=> '',
	'debug'			=> 'no',
	"mysql_host"		=> "localhost",
	"mysql_database"		=> "wikini",
	"mysql_user"		=> "wikini",
	"mysql_password"		=> '',
	"table_prefix"		=> "wikini_",
	"root_page"			=> "PagePrincipale",
	"wakka_name"		=> "MonSiteWikiNi",
	"base_url"			=> "http://".$_SERVER["SERVER_NAME"].($_SERVER["SERVER_PORT"] != 80 ? ":".$_SERVER["SERVER_PORT"] : "").$_SERVER["REQUEST_URI"].(preg_match("/".preg_quote("wakka.php")."$/", $_SERVER["REQUEST_URI"]) ? "?wiki=" : ""),
	"rewrite_mode"		=> (preg_match("/".preg_quote("wakka.php")."$/", $_SERVER["REQUEST_URI"]) ? "0" : "1"),
	'meta_keywords'		=> '',
	'meta_description'	=> '',
	"action_path"		=> "actions",
	"handler_path"		=> "handlers",
	"header_action"		=> "header",
	"footer_action"		=> "footer",
	"navigation_links"		=> "DerniersChangements :: DerniersCommentaires :: ParametresUtilisateur",
	"referrers_purge_time"	=> 24,
	"pages_purge_time"	=> 90,
	"default_write_acl"	=> "*",
	"default_read_acl"	=> "*",
	"default_comment_acl"	=> "*",
	"preview_before_save"	=> "0");

// load config
if (!$configfile = GetEnv("WAKKA_CONFIG")) $configfile = "wakka.config.php";
if (file_exists($configfile)) include($configfile);
$wakkaConfigLocation = $configfile;
$wakkaConfig = array_merge($wakkaDefaultConfig, $wakkaConfig);

// check for locking
if (file_exists("locked")) {
	// read password from lockfile
	$lines = file("locked");
	$lockpw = trim($lines[0]);
	
	// is authentification given?
	if (isset($_SERVER["PHP_AUTH_USER"])) {
		if (!(($_SERVER["PHP_AUTH_USER"] == "admin") && ($_SERVER["PHP_AUTH_PW"] == $lockpw))) {
			$ask = 1;
		}
	} else {
		$ask = 1;
	}
	
	if ($ask) {
		header("WWW-Authenticate: Basic realm=\"".$wakkaConfig["wakka_name"]." Install/Upgrade Interface\"");
		header("HTTP/1.0 401 Unauthorized");
		echo "Ce site est en cours de mise &agrave; jour. Veuillez essayer plus tard." ;
		exit;
	}
}


// compare versions, start installer if necessary
if ($wakkaConfig["wakka_version"] && (!$wakkaConfig["wikini_version"])) { $wakkaConfig["wikini_version"]=$wakkaConfig["wakka_version"]; }
if (($wakkaConfig["wakka_version"] != WAKKA_VERSION) || ($wakkaConfig["wikini_version"] != WIKINI_VERSION)) {
	// start installer
	if (!isset($_REQUEST["installAction"]) OR !$installAction = trim($_REQUEST["installAction"])) $installAction = "default";
	include("setup/header.php");
	if (file_exists("setup/".$installAction.".php")) include("setup/".$installAction.".php"); else echo "<i>Invalid action</i>" ;
	include("setup/footer.php");
	exit;
}


// configuration du cookie de session
//determine le chemin pour le cookie
$a = parse_url($wakkaConfig['base_url']);
$CookiePath = dirname($a['path']);
if ($CookiePath != '/') $CookiePath .= '/';
$a = session_get_cookie_params();
session_set_cookie_params($a['lifetime'],$CookiePath);
unset($a);
unset($CookiePath);

// start session
session_start();

// fetch wakka location
if (!isset($_REQUEST["wiki"])) $_REQUEST["wiki"] = '';
 
$wiki = $_REQUEST["wiki"];

// remove leading slash
$wiki = preg_replace("/^\//", "", $wiki);

// split into page/method
if (preg_match("#^(.+?)/([A-Za-z0-9_]*)$#", $wiki, $matches)) list(, $page, $method) = $matches;
else if (preg_match("#^(.*)$#", $wiki, $matches)) list(, $page) = $matches;

// create wiki object
$wiki = new Wiki($wakkaConfig);
// check for database access
if (!$wiki->dblink)
{
	echo "<p>Pour des raisons ind&eacute;pendantes de notre volont&eacute;, le contenu de ce Wiki est temporairement inaccessible. Veuillez r&eacute;essayer ult&eacute;rieurement, merci de votre compr&eacute;hension.</p>";
	exit;
}

function compress_output($output) 
{ 
	return gzencode($output); 
} 

// Check if the browser supports gzip encoding, HTTP_ACCEPT_ENCODING 
if (strstr ($HTTP_SERVER_VARS['HTTP_ACCEPT_ENCODING'], 'gzip') && function_exists('gzencode') )
{ 
	// Start output buffering, and register compress_output() (see 
	// below) 
	ob_start ("compress_output"); 

	// Tell the browser the content is compressed with gzip 
	header ("Content-Encoding: gzip"); 
} 


// go!
if (!isset($method)) $method='';

// Security (quick hack)  : Check method syntax
if (!(preg_match('#^[A-Za-z0-9_]*$#',$method))) {
	$method='';
}

$wiki->Run($page, $method);
?>
