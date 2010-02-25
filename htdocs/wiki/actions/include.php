<?php
/*
include.php : Permet d'inclure une page Wiki dans un autre page

Copyright 2003  Eric FELDSTEIN
Copyright 2003  Charles NEPOTE
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

/* Paramètres :
 -- page : nom wiki de la page a inclure (obligatoire)
 -- class : nom de la classe de style à inclure (facultatif)
*/ 


// récuperation du parametres
$incPageName = $this->GetParameter("page");
// TODO : améliorer le traitement des classes
if ($this->GetParameter("class")) {
	$classes='';
	$array_classes = explode(" ", $this->GetParameter("class"));
	foreach ($array_classes as $c) { $classes = $classes . "include_" . $c . " "; }
	}

// Affichage de la page ou d'un message d'erreur
if (empty($incPageName)) {
	echo $this->Format("//Le paramètre \"page\" est manquant.//");
} else {
	if (eregi("^".$incPageName."$",$this->GetPageTag())) {
		echo $this->Format("//Impossible à une page de s'inclure dans elle même.//");
	} else {
		if (!$this->HasAccess("read",$incPageName)){
			echo $this->Format("//Lecture de la page inclue $page non autorisée.//");
		} else {
			$incPage = $this->LoadPage($incPageName);
			$output = $this->Format($incPage["body"]);
			if ($classes) echo "<div class=\"", $classes,"\">\n", $output, "</div>\n";
			else echo $output;
		}
	}
}

?>