<?php
/*
listpages.php

Copyright 2002  David DELON
Copyright 2003  Patrick PAUL
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
	
if (!function_exists("TreeView"))
{
	function TreeView($node,$level,$indent=0) 
	{
		global $wiki;
		if ($level>0) {
			$head=split(" :: ",$wiki->GetConfigValue("navigation_links"));
			// we don't want page from the header  
			if (!in_array($node, $head, TRUE)) 
			{
				if (($indent>0) && (!($wiki->GetConfigValue("root_page")==$node)) || ($indent==0) )
				{
				// Ignore users too ...                
					if (!$wiki->LoadUser($node))
					{
						if ($indent)
							echo (str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",$indent)),$wiki->Link($node),"<br/>\n";
						$pages = $wiki->LoadAll("select to_tag from ".$wiki->config["table_prefix"]."links where from_tag='".mysql_escape_string($node)."' order by to_tag asc");

						if (is_array($pages)) {
							foreach ($pages as $page)
							{
								$wiki->CachePage($page);
								TreeView($page["to_tag"],$level-1,$indent+1);
							}
						}
					}
				}
			}
		}
	}
}

if($sortkey = $this->GetParameter("sort")) {
	if (($sortkey != "tag") && ($sortkey != "time") && ($sortkey != "owner") && ($sortkey != "user")) $sortkey = "tag";
	$pages = $this->LoadAll("select tag, owner, user from ".$this->config["table_prefix"]."pages where latest = 'Y' and comment_on = '' order by $sortkey asc");
        foreach ($pages as $page) {
                $this->CachePage($page);
		$owner=$page["owner"]?$page["owner"]:"Inconnu";
		echo "&nbsp;&nbsp;&nbsp;",$this->ComposeLinkToPage($page["tag"], "", "", 0)," . . . . ",$this->Format($owner),". . . . derni&egrave;re modification par " , $this->Format($page["user"]) , "<br/>\n" ;
        }
}
// Tree display
else if ($sortkey = $this->GetParameter("tree"))
{
// No rootpage specified, assume root_page
	if ($sortkey=="tree") $sortkey=$this->GetConfigValue("root_page"); 
      	echo $this->ComposeLinkToPage($sortkey),"<br /><br/>\n" ;

// 3 levels displayed, It should be parameter ...
	TreeView($sortkey,3);

}
// Default Action :  sort by tag
else 
{
	$pages = $this->LoadAll("select tag, owner, user from ".$this->config["table_prefix"]."pages where latest = 'Y' and comment_on = '' order by tag asc");
        foreach ($pages as $page) {
                $this->CachePage($page);
		$owner=$page["owner"]?$page["owner"]:"Inconnu";
		echo "&nbsp;&nbsp;&nbsp;",$this->ComposeLinkToPage($page["tag"], "", "", 0)," . . . . ",$this->Format($owner),"<br/>\n" ;
        }
}
?>
