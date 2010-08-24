<?php
$serveur = "mysql.hellosct1.nexenservices.com";
$user    = "hellosct1";
$passwd  = "HNgNMQN6";
$bdd     = "hellosct1";
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