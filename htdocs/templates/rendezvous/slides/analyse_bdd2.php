<?php
$serveur = "localhost";
$user    = "root";
$passwd  = "root";
$bdd     = "robotsphp";

$cnx = mysql_pconnect($serveur, $user, $passwd);
mysql_select_db($bdd,$cnx);
$rows = mysql_query("SHOW TABLE STATUS");  

while ($row = mysql_fetch_array($rows)) { 
echo "Nom de la table : ".$row['Name']."<br>";
echo "Nombre de lignes : ".$row['Rows']."<br>";
echo "Espace occupe de la table (database + index) : ".($row['Data_length'] + $row['Index_length'])."<br>";
echo "Maximum prevu de la table : ".$row['Max_data_length']."<br>";
echo "<hr>";
} 

?>
