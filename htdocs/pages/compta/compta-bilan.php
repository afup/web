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
echo "Bilan ";
echo "<a href='compta-bilan-pdf.php?idperiode=$idperiode' class='links' target='_bank'>PDF</a>";

echo "<table border=0 width=100%>";
echo "<tr align='center'><td>Depense</td><td>Recette</td></tr>";

echo "<tr>";
echo "<td width=50% valign=top>";

$sql="SELECT sum(a.montant) as montant,b.id,b.evenement  
	FROM compta a, compta_evenement b 
	WHERE a.idoperation=1 AND a.idevenement=b.id 
		  AND a.date_ecriture>='$periode_debut' AND a.date_ecriture<='$periode_fin'   
	GROUP BY b.evenement 
	ORDER BY b.evenement,a.date_ecriture";

assert ('$cnx->prepare($sql)');
$qid=$cnx->prepare($sql);
$qid->execute();

echo "<table width=100% border=1 cellspacing='0' cellpadding='0'>";
echo "<tr>";
echo "<td colspan='2'>Evenement</td><td>Montant</td>";
echo "</tr>";

while( $row=$qid->fetch(PDO::FETCH_OBJ) ) 
{
	echo "<tr>";
	echo "<td valign='top'>".$row->evenement."</td>";
	echo "<td>";
//	echo " <a href=compta-bilan.php?idevenement=$row->id&idperiode=$idperiode class='links'>Details</a>";

	echo " <a href=compta-bilan.php?idevenement=$row->id&idperiode=$idperiode class='links'>Details</a>";
	echo " - ";
	echo " <a href=compta-bilan-view.php?idevenement=$row->id&idperiode=$idperiode class='links'>View</a>";
	echo " - ";
	echo "<a href='compta-bilan-view-pdf.php?idevenement=$row->id&idperiode=$idperiode' class='links' target='_bank'>PDF</a>";
	
	
	if ($row->id==$idevenement) detail_bilan ($row->id,$periode_debut,$periode_fin,1,$idperiode);
	
	echo "</td>";

	echo "<td valign='top' align='right' width='80'>".fprix($row->montant)."</td>";
	echo "</tr>";
$depense+=$row->montant;
}
echo "</table>";

echo "</td><td valign=top>";

$sql="SELECT sum(a.montant) as montant,b.id,b.evenement  
	FROM compta a, compta_evenement b 
	WHERE a.idoperation=2 AND a.idevenement=b.id 
		  AND a.date_ecriture>='$periode_debut' AND a.date_ecriture<='$periode_fin'   
	GROUP BY b.evenement 
	ORDER BY b.evenement,a.date_ecriture";

assert ('$cnx->prepare($sql)');
$qid=$cnx->prepare($sql);
$qid->execute();


echo "<table width=100% border=1 cellspacing='0' cellpadding='0'>";
echo "<tr>";
echo "<tr align='center'><td colspan='2'>Evenement</td><td>Recette</td></tr>";
echo "</tr>";

while( $row=$qid->fetch(PDO::FETCH_OBJ) ) 
{
	echo "<tr>";
	echo "<td valign='top' >".$row->evenement."</td>";
	echo "<td>";
//	echo " <a href=compta-bilan.php?idevenement=$row->id&idperiode=$idperiode class='links'>view</a>";
	echo " <a href=compta-bilan.php?idevenement=$row->id&idperiode=$idperiode class='links'>Details</a>";
	echo " - ";
	echo " <a href=compta-bilan-view.php?idevenement=$row->id&idperiode=$idperiode class='links'>View</a>";
	echo " - ";
	echo "<a href='compta-bilan-pdf.php?idevenement=$row->id&idperiode=$idperiode' class='links' target='_bank'>PDF</a>";
	
	if ($row->id==$idevenement) detail_bilan ($row->id,$periode_debut,$periode_fin,2,$idperiode);
	echo "</td>";
	echo "<td valign='top' align='right' width='80'>".fprix($row->montant)."</td>";
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
function detail_bilan($id,$periode_debut,$periode_fin,$details,$idperiode)
{
global $serveur,$port,$bdd,$user,$passwd;

	echo "<table border='1'>";
echo "<tr><td>Date ecriture</td><td>Description</td><td>Montant</td></tr>";
$cnx2 = new PDO('mysql:host='.$serveur.';port='.$port.';dbname='.$bdd, $user, $passwd);
$sql2="SELECT * 
	   FROM compta 
	   WHERE idoperation='$details' AND idevenement=$id AND date_ecriture>='$periode_debut' AND date_ecriture<='$periode_fin' 
	  ORDER BY date_ecriture,idevenement,idcategorie";

$qid2=$cnx2->prepare($sql2);
$qid2->execute();
while( $row2=$qid2->fetch(PDO::FETCH_OBJ) ) 
{
	echo "<tr>";
	echo "<td>".datefr($row2->date_ecriture)."</td>";
	echo "<td>".$row2->description." (".recup_reglement($row2->idmode_regl)." ".$row2->obs_regl.")</td>";

	echo "<td width='70' align='right'>".fprix($row2->montant)."</td>";
	echo "<td><a href='compta-saisie.php?id=".$row2->id."&idperiode=$idperiode' class=links>Edit</a></td>";
		
	echo "</tr>";
}

echo "</table>";

	$cnx2=NULL;
}


?>
