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

$depense=0;
$recette=0;
echo recup_evenement($idevenement)." ";
echo "<a href=compta-bilan-view-pdf.php?idevenement=$idevenement&idperiode=$idperiode class='links' target='_bank'> PDF</a>";
echo "<table border=0 width=100%>";
echo "<tr align='center'><td>Depense</td><td>Recette</td></tr>";

echo "<tr>";
echo "<td width=50% valign=top>";

/*
$sql="SELECT * 
	   FROM compta 
	   WHERE idoperation='1' AND idevenement=$idevenement 
	         AND date_ecriture>='$periode_debut' AND date_ecriture<='$periode_fin' 
	  ORDER BY date_ecriture,idevenement,idcategorie";
*/
$sql="SELECT compta.*,compta_categorie.id,compta_categorie.categorie   
	FROM compta, compta_categorie  
	WHERE compta.idoperation='1' AND compta.idevenement=$idevenement 
	         AND compta.date_ecriture>='$periode_debut' AND compta.date_ecriture<='$periode_fin' 
		  AND compta.idcategorie = compta_categorie.id     
	ORDER BY compta.date_ecriture,compta.idevenement,compta.idcategorie";


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
 * 
$sql="SELECT * 
	   FROM compta 
	   WHERE idoperation='2' AND idevenement=$idevenement 
	         AND date_ecriture>='$periode_debut' AND date_ecriture<='$periode_fin' 
	  ORDER BY date_ecriture,idevenement,idcategorie";
*/
$sql="SELECT compta.*,compta_categorie.id,compta_categorie.categorie   
	FROM compta, compta_categorie  
	WHERE compta.idoperation='2' AND compta.idevenement=$idevenement 
	         AND compta.date_ecriture>='$periode_debut' AND compta.date_ecriture<='$periode_fin' 
		  AND compta.idcategorie = compta_categorie.id     
	ORDER BY compta.date_ecriture,compta.idevenement,compta.idcategorie";

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