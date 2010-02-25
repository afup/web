<?php

// Action changesstyle.php version 0.2 du 16/03/2004
// pour WikiNi 0.4.1rc (=> à la version du 200403xx) et supérieurs
// Par Charles Népote (c) 2004
// Licence GPL


// Fonctionnement
//
// Cette action regroupe la fonction de changement de style ainsi que l'interface
// de modification du style.
// Une fois le style sélectionné via l'interface, la requête est envoyée sous la forme :
// http://example.org/PageTest&set="NomDeFeuilleDeStyle"
// . si ce nom n'est pas constitué uniquement de caractères alphanumériques,
//   une erreur est retournée
// . si ce nom est valide et que la feuille de style existe :
//   . on change le cookie utilisateur
//   . on redirrige l'utilisateur vers http://example.org/PageTest où
//     l'utilisateur peut alors constater le changement de style


// Usage :
//
// -- {{changestyle link="xxx.css"}}
//    donne le lien suivant :
//    Feuille de style xxx.css
//
// -- {{changestyle link="xxx.css" title="Ouragan"}}
//    donne le lien suivant :
//    Ouragan


// A compléter (peut-être un jour) :
//
// -- {{changestyle}}
//    donne un formulaire :
//    Entrer l'adresse de la feuille de style désirée : [     ]
//
// -- {{changestyle choice="zzz.css;ttt.css"}}
//	[] Feuille de style zzz
//	[] Feuille de style ttt


$set = $_GET["set"];


if ($this->GetParameter(link))
{
	echo	"<a href=\"".$this->href()."&set=".$this->GetParameter(link)."\">";
	echo	(!$this->GetParameter(title))?"Feuille de style ".$this->GetParameter(link):$this->GetParameter(title);
	echo	"</a>";
}


// Do it.
if (preg_match("/^[A-Za-z0-9][A-Za-z0-9]+$/", $set))
{
	$this->SetPersistentCookie('sitestyle',$set,1);
	header("Location: ".$this->href());
}
else if ($set)
{
	$this->SetMessage("La feuille de style ".$set." est non valide !");
	header("Location: ".$this->href());
}
?>