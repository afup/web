<?php
$serveur = "localhost";
$user    = "root";
$passwd  = "root";
$bdd     = "robotsphp";

$cnx = mysql_pconnect($serveur, $user, $passwd) or trigger_error(mysql_error(),E_USER_ERROR);
mysql_select_db($bdd,$cnx);

$sql="SHOW TABLE STATUS";
$rows = mysql_query($sql);  
if (!$rows)  die ("Requete invalide :  " . mysql_error());
//mysql_fetch_object( $qid);     	
//print_r($rows);


while ($row = mysql_fetch_array($rows)) { 
echo "<pre>";
print_r($row);
//die();
$dbSize += $row['Data_length'] + $row['Index_length']; 
print '<pre>Table: <strong>' . $row['Name'] . '</strong><br />'; 
print 'nbre ligne rows. . .: ' . $row['Rows'] . '<br />'; 
print 'Database size. . .: ' . $row['Data_length'] . '<br />'; 
print 'Index Size . . . .: ' . $row['Index_length'] . '<br />'; 
print 'Total size . . . .: ' . ($row['Data_length'] + $row['Index_length']) . '<br /></pre>'; 
echo "max : ".$row['Max_data_length']."<br>";
} 


?>
