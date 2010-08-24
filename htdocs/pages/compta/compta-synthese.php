<?php 
require_once "include/config.inc.php";
?>
<html>
<head>
<link rel="stylesheet" href="include/css.css" type="text/css">
</head>
<body>
<?php require_once "ihead.inc.php"; ?>

<?php
$idevenement=verif_GetPost($_GET['idevenement']);;


echo "<form action=".htmlentities($_SERVER['PHP_SELF'])."  method='GET'>";
$cnx8 = new PDO('mysql:host='.$serveur.';port='.$port.';dbname='.$bdd, $user, $passwd);

$sql8="SELECT * FROM compta_evenement ORDER BY evenement";
$even8=$cnx8->prepare($sql8);
$even8->execute();

	echo "<select name='idevenement'>";

	while( $row8=$even8->fetch(PDO::FETCH_ASSOC) )       
	{
		echo ligne_selected($row8[evenement],$row8['id'],$idevenement);  
	}
	echo "</select>";
	$cnx8=NULL;
	echo "<input type='submit' name='action' value='ok'>";
echo "</form>";	

if (!$idevenement) exit;

$depense=0;
$recette=0;
echo "Synthese de l'evenement ".recup_evenement($idevenement)." ";
echo "<table border=0 width=100%>";
echo "<tr align='center'><td>Depense</td><td>Recette</td></tr>";

echo "<tr>";
echo "<td width=50% valign=top>";
/*
$sql="SELECT * 
	   FROM compta 
	   WHERE idoperation='1' AND idevenement=$idevenement 
	  ORDER BY idevenement,idcategorie";*/

$sql="SELECT compta.*,compta_categorie.id,compta_categorie.categorie   
	FROM compta, compta_categorie  
	WHERE compta.idoperation='1' AND compta.idevenement=$idevenement
		  AND compta.idcategorie = compta_categorie.id     
	ORDER BY compta_categorie.categorie,compta.date_ecriture";



//assert ('$cnx->prepare($sql)');
$qid=$cnx->prepare($sql);
$qid->execute();


echo "<table width=100% border=1 cellspacing='0' cellpadding='0'>";
echo "<tr>";
echo "<td>Categorie</td><td>Description</td><td>Montant</td>";
echo "</tr>";

while( $row=$qid->fetch(PDO::FETCH_OBJ) ) 
{
	echo "<tr>";
	echo "<td>".$row->categorie."</td>";
	echo "<td>".$row->description."</td>";
	echo "<td align='right' width='80'>".fprix($row->montant)."</td>";
	echo "</tr>";
$depense+=$row->montant;
}
echo "</table>";

echo "</td><td valign=top>";
/*
$sql="SELECT * 
	   FROM compta 
	   WHERE idoperation='2' AND idevenement=$idevenement 
	  ORDER BY idevenement,idcategorie";
*/
$sql="SELECT compta.*,compta_categorie.id,compta_categorie.categorie   
	FROM compta, compta_categorie  
	WHERE compta.idoperation='2' AND compta.idevenement=$idevenement
		  AND compta.idcategorie = compta_categorie.id     
	ORDER BY compta.date_ecriture";


assert ('$cnx->prepare($sql)');
$qid=$cnx->prepare($sql);
$qid->execute();


echo "<table width=100% border=1 cellspacing='0' cellpadding='0'>";
echo "<tr>";
echo "<td>Categorie</td><td>Description</td><td>Montant</td>";
echo "</tr>";

while( $row=$qid->fetch(PDO::FETCH_OBJ) ) 
{
	echo "<tr>";
	echo "<td>".$row->categorie."</td>";
	echo "<td>".$row->description."</td>";
	echo "<td align='right' width='80'>".fprix($row->montant)."</td>";
	echo "</tr>";
$recette +=$row->montant;
}
echo "</table>";

echo "</td>";
echo "</tr>";
echo "<tr>";
echo "<td align=center>".fprix($depense)."</td>";
echo "<td align=center>".fprix($recette)."</td>";
echo "</tr>";
echo "<tr>";
$total=$recette-$depense;
echo "<td colspan=2 align=center>".fprix($total)."</td>";

echo "</tr>";
echo "</table>";

$qid->closeCursor();
$cnx = null;

require_once "ifooter.inc.php";
?>
</body></html>