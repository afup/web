<?php
/*
redirect.php : Permet de faire une redirection vers une autre pages Wiki du site

Copyright 2003  Eric FELDSTEIN
Copyright 2003  David DELON
Copyright 2004  Jean Christophe ANDRE
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

/*
Parametres : page : nom wiki de la page vers laquelle ont doit rediriger (obligatoire)
exemple : {{redirect page="BacASable"}}
*/

//recuperation du parametres
$redirPageName = $this->GetParameter("page");

if (empty($redirPageName)){
	echo $this->Format("//Le param&ecirc;tre \"page\" est manquant.//");
}else{
	if (eregi("^".$redirPageName."$",$this->GetPageTag())){
		echo $this->Format("//Impossible &agrave; une page de se rediriger vers elle m&ecirc;me.//");
	}else{
		$fromPages = array();
		$fromPages = explode(":",$_COOKIE['redirectfrom']);
		if (in_array($this->GetPageTag(),$fromPages)){
			echo $this->Format("//Redirection circulaire.//");
		}else{
			$fromPages[] = $this->GetPageTag();
			SetCookie('redirectfrom', implode(":",$fromPages), time() + 30, $this->CookiePath);
			$this->Redirect($this->Href('', $redirPageName));
		}
	}
}
?>