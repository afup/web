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
echo recup_budget_evenement($idevenement)." ";
echo "<a href=budget-view-details-pdf.php?idevenement=$idevenement&idperiode=$idperiode class='links' target='_bank'> PDF</a>";
echo "<table border=0 width=100%>";
echo "<tr align='center'><td>Depense</td><td>Recette</td></tr>";

echo "<tr>";
echo "<td width=50% valign=top>";

$sql="SELECT budget.*, budget_categorie.categorie, budget_evenement.evenement 
FROM budget_evenement INNER JOIN (budget_categorie INNER JOIN budget ON budget_categorie.id = budget.idcategorie) ON budget_evenement.id = budget.idevenement
WHERE budget.idevenement='$idevenement' AND idoperation='1' 
	  AND periode>='$periode_debut' AND periode<='$periode_fin' 
ORDER BY budget.idoperation, budget_categorie.categorie,budget.description  
"; 


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
	echo "<td align='right' width='80'>".fprix($row->montant_theo)."</td>";
	echo "</tr>";
$depense+=$row->montant_theo;
}
echo "</table>";

echo "</td><td valign=top>";

$sql="SELECT budget.*, budget_categorie.categorie, budget_evenement.evenement 
FROM budget_evenement INNER JOIN (budget_categorie INNER JOIN budget ON budget_categorie.id = budget.idcategorie) ON budget_evenement.id = budget.idevenement
WHERE budget.idevenement='$idevenement' AND budget.idoperation='2' 
      AND periode>='$periode_debut' AND periode<='$periode_fin' 
ORDER BY budget.idoperation, budget_categorie.categorie,budget.description  
";
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
	echo "<td align='right' width='80'>".fprix($row->montant_theo)."</td>";
	echo "</tr>";
$recette +=$row->montant_theo;
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