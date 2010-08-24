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
echo "Simulation ";

echo "<table border=0 width=100%>";
echo "<tr align='center'><td>Depense</td><td>Recette</td></tr>";

echo "<tr>";
echo "<td width=50% valign=top>";

$sql="SELECT sum(a.montant_theo) as montant,b.id,b.evenement  
	FROM compta_simulation a, budget_evenement b 
	WHERE a.idoperation=1 AND a.idevenement=b.id 
		  AND a.periode>='$periode_debut' AND a.periode<='$periode_fin'   
	GROUP BY b.evenement 
	ORDER BY b.evenement,a.periode";

//assert ('$cnx->prepare($sql)');
$qid=$cnx->prepare($sql);
$qid->execute();


echo "<table width=100% border=1 cellspacing='0' cellpadding='0'>";
echo "<tr>";
echo "<td>Evenement</td><td>&nbsp;</td><td>Montant</td>";
echo "</tr>";

while( $row=$qid->fetch(PDO::FETCH_OBJ) ) 
{
	echo "<tr>";
	echo "<td valign=top>".$row->evenement."</td>";
	echo "<td>";
	echo " <a href=compta-simul-view.php?idevenement=$row->id&idperiode=$idperiode class='links'>Details</a>";
	echo " - ";
	echo "<a href='compta-simul-view-details-pdf.php?idevenement=$row->id&idperiode=$idperiode' class='links' target='_bank'>PDF</a>";
	
	if ($row->id==$idevenement) budget_details ($row->id,$periode_debut,$periode_fin,1,$idperiode);
	
	echo "</td>";


	echo "<td valign=top align='right' width='80'>".fprix($row->montant)."</td>";
	echo "</tr>";
$depense+=$row->montant;
}
echo "</table>";

echo "</td><td valign=top>";

$sql="SELECT sum(a.montant_theo) as montant,b.id,b.evenement  
	FROM compta_simulation a, budget_evenement b 
	WHERE a.idoperation=2 AND a.idevenement=b.id 
		  AND a.periode>='$periode_debut' AND a.periode<='$periode_fin'   
	GROUP BY b.evenement 
	ORDER BY b.evenement,a.periode";

$qid=$cnx->prepare($sql);
$qid->execute();


echo "<table width=100% border=1 cellspacing='0' cellpadding='0'>";
echo "<tr>";
echo "<tr align='center'><td>Depense</td><td>&nbsp;</td><td>Recette</td></tr>";
echo "</tr>";

while( $row=$qid->fetch(PDO::FETCH_OBJ) ) 
{
	echo "<tr>";
	echo "<td valign=top>".$row->evenement."</td>";
	echo "<td>";
	echo " <a href=compta-simul-view.php?idevenement=$row->id&idperiode=$idperiode class='links'>Details</a>";
	echo " - ";
	echo "<a href=compta-simul-view-pdf.php?idevenement=$row->id&idperiode=$idperiode' class='links' target='_bank'>PDF</a>";
		
	if ($row->id==$idevenement) budget_details($row->id,$periode_debut,$periode_fin,2,$idperiode);
	echo "</td>";
	echo "<td valign=top align='right' width='80'>".fprix($row->montant)."</td>";
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

<?php
//---------------------------
function budget_details($id,$periode_debut,$periode_fin,$details,$idperiode)
{
global $serveur,$port,$bdd,$user,$passwd;

	echo "<table border='1'>";
echo "<tr><td>Date ecriture</td><td>Description</td><td>Montant</td></tr>";
$cnx2 = new PDO('mysql:host='.$serveur.';port='.$port.';dbname='.$bdd, $user, $passwd);
$sql2="SELECT a.*,a.id as idtmp,b.id,b.categorie 
	   FROM compta_simulation a, budget_categorie b 
	   WHERE a.idoperation='$details' 
	         AND a.idevenement=$id 
	   		 AND a.periode>='$periode_debut' AND a.periode<='$periode_fin' 
		  AND a.idcategorie = b.id     
	  ORDER BY a.periode,a.idevenement,b.categorie";


$qid2=$cnx2->prepare($sql2);
$qid2->execute();
while( $row2=$qid2->fetch(PDO::FETCH_OBJ) ) 
{
	echo "<tr>";
//	echo "<td>".datefr($row2->periode)."</td>";
	//echo "<td>".recup_categorie($row2->idcategorie)."</td>";
	echo "<td>".$row2->categorie."</td>";
		echo "<td>".$row2->description."</td>";
	
		echo "<td>".fprix($row2->montant_theo)."</td>"; 
	echo "<td><a href='compta-simul-saisie.php?id=".$row2->idtmp."&idperiode=$idperiode' class=links>Edit</a></td>";
	echo "</tr>";
}

echo "</table>";

	$cnx2=NULL;
}



?>
