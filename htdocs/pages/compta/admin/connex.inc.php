<?php
$serveur = "localhost";
$user    = "root";
$passwd  = "";
$bdd     = "afupcompta";
$port='3306';

try 
{
   $cnx = new PDO('mysql:host='.$serveur.';port='.$port.';dbname='.$bdd, $user, $passwd);
	$cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
}


catch (PDOException $error) 
{	
echo 'N° : '.$error->getCode().'<br />';
die ('Erreur : '.$error->getMessage().'<br />');
}

?>