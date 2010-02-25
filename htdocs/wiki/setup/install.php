<?php
/*
install.php
Copyright (c) 2002, Hendrik Mans <hendrik@mans.de>
Copyright 2002, 2003 David DELON
Copyright 2002, 2003 Patrick PAUL
Copyright  2003  Eric FELDSTEIN
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

// fetch configuration
$config = $_POST["config"];

// test configuration
echo "<b>Test de la configuration</b><br>\n";
test("Test connexion MySQL ...", $dblink = @mysql_connect($config["mysql_host"], $config["mysql_user"], $config["mysql_password"]));
test("Recherche base de donn&eacute;es ...", @mysql_select_db($config["mysql_database"], $dblink), "La base de donn&eacute;es que vous avez choisie n'existe pas, vous devez la cr&eacute;er avant d'installer WikiNi !");
echo "<br>\n" ;

// do installation stuff
if (!$version = trim($wakkaConfig["wikini_version"])) $version = "0";
switch ($version)
{
// new installation
case "0":
	echo "<b>Installation</b><br>\n";
	test("Creation table page...",
		@mysql_query(
			"CREATE TABLE ".$config["table_prefix"]."pages (".
  			"id int(10) unsigned NOT NULL auto_increment,".
  			"tag varchar(50) NOT NULL default '',".
  			"time datetime NOT NULL default '0000-00-00 00:00:00',".
  			"body text NOT NULL,".
  			"body_r text NOT NULL,".
  			"owner varchar(50) NOT NULL default '',".
  			"user varchar(50) NOT NULL default '',".
  			"latest enum('Y','N') NOT NULL default 'N',".
  			"handler varchar(30) NOT NULL default 'page',".
  			"comment_on varchar(50) NOT NULL default '',".
  			"PRIMARY KEY  (id),".
  			"FULLTEXT KEY tag (tag,body),".
  			"KEY idx_tag (tag),".
  			"KEY idx_time (time),".
  			"KEY idx_latest (latest),".
  			"KEY idx_comment_on (comment_on)".
			") TYPE=MyISAM;", $dblink), "D&eacute;j&agrave; cr&eacute;&eacute;e ?", 0);
	test("Creation table ACL ...",
		@mysql_query(
			"CREATE TABLE ".$config["table_prefix"]."acls (".
  			"page_tag varchar(50) NOT NULL default '',".
			"privilege varchar(20) NOT NULL default '',".
  			"list text NOT NULL,".
 			"PRIMARY KEY  (page_tag,privilege)".
			") TYPE=MyISAM", $dblink), "D&eacute;j&agrave; cr&eacute;&eacute;e ?", 0);
	test("Creation table link ...",
		@mysql_query(
			"CREATE TABLE ".$config["table_prefix"]."links (".
			"from_tag char(50) NOT NULL default '',".
  			"to_tag char(50) NOT NULL default '',".
  			"UNIQUE KEY from_tag (from_tag,to_tag),".
  			"KEY idx_from (from_tag),".
  			"KEY idx_to (to_tag)".
			") TYPE=MyISAM", $dblink), "D&eacute;j&agrave; cr&eacute;&eacute;e ?", 0);
	test("Creation table referrer ...",
		@mysql_query(
			"CREATE TABLE ".$config["table_prefix"]."referrers (".
  			"page_tag char(50) NOT NULL default '',".
  			"referrer char(150) NOT NULL default '',".
  			"time datetime NOT NULL default '0000-00-00 00:00:00',".
  			"KEY idx_page_tag (page_tag),".
  			"KEY idx_time (time)".
			") TYPE=MyISAM", $dblink), "D&eacute;j&agrave; cr&eacute;&eacute;e ?", 0);
	test("Creation table user ...",
		@mysql_query(
			"CREATE TABLE ".$config["table_prefix"]."users (".
  			"name varchar(80) NOT NULL default '',".
  			"password varchar(32) NOT NULL default '',".
  			"email varchar(50) NOT NULL default '',".
  			"motto text NOT NULL,".
  			"revisioncount int(10) unsigned NOT NULL default '20',".
  			"changescount int(10) unsigned NOT NULL default '50',".
  			"doubleclickedit enum('Y','N') NOT NULL default 'Y',".
  			"signuptime datetime NOT NULL default '0000-00-00 00:00:00',".
  			"show_comments enum('Y','N') NOT NULL default 'N',".
  			"PRIMARY KEY  (name),".
  			"KEY idx_name (name),".
  			"KEY idx_signuptime (signuptime)".
			") TYPE=MyISAM", $dblink), "D&eacute;j&agrave; cr&eacute;&eacute;e ?", 0);
			
		@mysql_query("INSERT INTO ".$config["table_prefix"]."acls VALUES ('ParametresUtilisateur', 'write', '!*')");
		@mysql_query("INSERT INTO ".$config["table_prefix"]."acls VALUES ('ParametresUtilisateur', 'read', '*')");

	//insertion des pages de documentation et des pages standards 
	$d = dir("setup/doc/");
	while ($doc = $d->read()){
		if ($doc != "." && $doc != ".." && !is_dir($doc)){
			$pagecontent = implode ('', file("setup/doc/$doc"));
			if ($doc=='_root_page.txt'){
				$pagename = $config["root_page"];
			}else{
				$pagename = substr($doc,0,strpos($doc,'.txt'));
			}

			$sql = "Select tag from ".$config["table_prefix"]."pages where tag='$pagename'";
		
			// Insert documentation page if not present (a previous failed installation ?)
			if (($r=@mysql_query($sql, $dblink)) && (mysql_num_rows($r)==0)) {
			
				$sql = "Insert into ".$config["table_prefix"]."pages ".
				"set tag = '$pagename', ".
				"body = '".mysql_escape_string($pagecontent)."', ".
				"user = 'WikiNiInstaller', ".
				"time = now(), ".
				"latest = 'Y'";

				test("Insertion de la page $pagename ...", @mysql_query($sql, $dblink),"?",0);

				// update table_links 
				$wiki = new Wiki($config);
				$wiki->SetPage($wiki->LoadPage($pagename,"",0));
				$wiki->ClearLinkTable();
				$wiki->StartLinkTracking();
				$wiki->TrackLinkTo($pagename);
				$dummy = $wiki->Header();
				$dummy .= $wiki->Format($pagecontent);
				$dummy .= $wiki->Footer();
				$wiki->StopLinkTracking();
				$wiki->WriteLinkTable();
				$wiki->ClearLinkTable();
			}
			else
			{
				test("Insertion de la page $pagename ...", 0 ,"Existe d&eacute;j&agrave;.",0);
			}	

		}
	}
	break;
	
	// The funny upgrading stuff. Make sure these are in order! //
case "0.1":
	echo "<b>En cours de mise &agrave; jour de WikiNi 0.1</b><br>\n";
	test("Just very slightly altering the pages table...", 
		@mysql_query("alter table ".$config["table_prefix"]."pages add body_r text not null default '' after body", $dblink), "Already done? Hmm!", 0);
	test("Claiming all your base...", 1);
}

?>

<p>
A l'&eacute;tape suivante, le programme d'installation va essayer
d'&eacute;crire le fichier de configuration <tt><?php echo  $wakkaConfigLocation ?></tt>.
Assurez vous que le serveur web a bien le droit d'&eacute;crire dans ce fichier, sinon vous devrez le modifier manuellement.  </p>

<form action="<?php echo  myLocation(); ?>?installAction=writeconfig" method="POST">
<input type="hidden" name="config" value="<?php echo  htmlentities(serialize($config)) ?>">
<input type="submit" value="Continuer">
</form>
