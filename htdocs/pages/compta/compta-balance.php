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

$idevenement=verif_GetPost($_GET['idevenement']);
$tri=verif_GetPost($_GET['tri']);
$ordre=verif_GetPost($_GET['ordre']);
if ($tri=="date") $order="ORDER BY date_ecriture ";
if ($tri=="regle") $order="ORDER BY date_regl ";

if ($tri && $ordre!="DESC") $ordre = "DESC"; else $ordre = "";


$sql="SELECT a.date_ecriture, a.idevenement, b.* 
	  FROM compta a,compta_evenement b  
	  WHERE a.idevenement=b.id 
	        AND date_ecriture>='$periode_debut' AND date_ecriture<='$periode_fin' 
	  GROUP BY b.evenement 
	  ORDER BY b.evenement";

$qid=$cnx->prepare($sql);
$qid->execute();
echo "Balance<br>";

echo "<table border=1 cellspacing='0' cellpadding='2'>";
echo "<tr><td>Evenement</td><td>&nbsp;</td><td>Depense</td><td>Recette</td></tr>";
while( $row=$qid->fetch(PDO::FETCH_OBJ) ) 
{
	echo "<tr>";
	echo "<td valign='top'>".$row->evenement."</td>";
	echo "<td valign='top'>";
	echo "<a href=compta-balance.php?idevenement=$row->id&idperiode=$idperiode class='links'>details</a>";
	echo " - ";
	echo "<a href=compta-balance-view.php?idevenement=$row->id&idperiode=$idperiode class='links'>View</a>";
	echo "</td>";

	$bal_depense=calcul_balance_evenement($row->idevenement,1,$periode_debut,$periode_fin);
	$montant_bal_depense +=$bal_depense;
	echo "<td valign='top' align='right'>".fprix($bal_depense)."</td>";

	$bal_recette=calcul_balance_evenement($row->idevenement,2,$periode_debut,$periode_fin);
	$montant_bal_recette +=$bal_recette;
	echo "<td valign='top' align='right'>".fprix($bal_recette)."</td>";
	echo "</tr>";
	if ($idevenement!="" && $row->id==$idevenement) 
	{
		echo "<tr><td colspan='2'>";
		balance_categorie($row->id,$idevenement,$periode_debut,$periode_fin,$idperiode);
		echo "</td></tr>";
	}
}
echo "<tr><td colspan=2 align='right'>Total</td>";
echo "<td>".fprix($montant_bal_depense)."</td>";
echo "<td>".fprix($montant_bal_recette)."</td>";
echo "</tr>";
echo "<tr><td colspan=2 align='right'>Solde</td>";
$dif=$montant_bal_recette-$montant_bal_depense;
echo "<td colspan='2' align='center'>".fprix($dif)."</td>";
echo "</tr>";

echo "</table>";

$qid->closeCursor();
$cnx = null;

require_once "ifooter.inc.php";
?>
</body></html>


<?php
function calcul_balance_evenement($idevenement,$idoperation,$periode_debut,$periode_fin)
{
global $serveur,$port,$bdd,$user,$passwd;
	$cnx4 = new PDO('mysql:host='.$serveur.';port='.$port.';dbname='.$bdd, $user, $passwd);

	$sql4="SELECT SUM(montant) as total,idoperation,idevenement 
	  FROM compta   
	  WHERE idevenement=$idevenement AND idoperation=$idoperation  
	        AND date_ecriture>='$periode_debut' AND date_ecriture<='$periode_fin' 
	  GROUP BY idoperation,idevenement 
	  ";

		$qid4=$cnx4->prepare($sql4);
		$qid4->execute();
		$row4=$qid4->fetch(PDO::FETCH_OBJ); 
		return $row4->total;

}

function balance_categorie($id,$idevenement,$periode_debut,$periode_fin,$idperiode)
{
global $serveur,$port,$bdd,$user,$passwd;
//	if ($id==$idevenement) 
//	{
//		echo "<tr><td>";
		$cnx3 = new PDO('mysql:host='.$serveur.';port='.$port.';dbname='.$bdd, $user, $passwd);

	$sql3="SELECT a.date_ecriture, a.idevenement,a.idcategorie, b.id,b.categorie 
	  FROM compta a,compta_categorie b  
	  WHERE a.idevenement=$idevenement AND a.idcategorie=b.id 
	        AND date_ecriture>='$periode_debut' AND date_ecriture<='$periode_fin' 
	  GROUP BY b.categorie
	  ORDER BY b.categorie";

		$qid3=$cnx3->prepare($sql3);
		$qid3->execute();
		while( $row3=$qid3->fetch(PDO::FETCH_OBJ) ) 
		{		
			detail ($row3->idevenement,$row3->idcategorie,$periode_debut,$periode_fin,$idperiode);
			echo "<br>";
		}
	//	echo "</td></tr>";
	//}
	
}

function detail($idevenement,$idcategorie,$periode_debut,$periode_fin,$idperiode)
{
global $serveur,$port,$bdd,$user,$passwd;

	echo "<table border='1' width='100%' cellspacing='0' cellpadding='2'>";
echo "<tr><td>Date ecriture</td><td>Categorie</td><td>Description</td><td>Depense</td><td>Recette</td><td>Edit</td></tr>";
$cnx2 = new PDO('mysql:host='.$serveur.';port='.$port.';dbname='.$bdd, $user, $passwd);
/*
$sql2="SELECT * 
	   FROM compta  
	   WHERE idevenement=$idevenement AND idcategorie=$idcategorie AND date_ecriture>='$periode_debut' AND date_ecriture<='$periode_fin' 
	  ORDER BY date_ecriture,idevenement,idcategorie";
*/
$sql2="SELECT compta.*,compta.id as idtmp,compta_categorie.id,compta_categorie.categorie   
	FROM compta, compta_categorie  
	WHERE compta.idevenement=$idevenement AND compta.idcategorie=$idcategorie AND compta.date_ecriture>='$periode_debut' AND compta.date_ecriture<='$periode_fin'
		  AND compta.idcategorie = compta_categorie.id     
	ORDER BY compta.date_ecriture,compta.idevenement,compta.idcategorie";


$qid2=$cnx2->prepare($sql2);
$qid2->execute();
while( $row2=$qid2->fetch(PDO::FETCH_OBJ) ) 
{
	echo "<tr>";
	echo "<td>".datefr($row2->date_ecriture)."</td>";
	echo "<td>".$row2->categorie."</td>";
	echo "<td>".$row2->description." (".recup_reglement($row2->idmode_regl)." ".$row2->obs_regl.")</td>";
	if ($row2->idoperation=="1") 
	{
		echo "<td align='right'>".fprix($row2->montant)."</td><td>&nbsp;</td>"; 
		$depense += $row2->montant;
	} else { 
		echo "<td align='right'>&nbsp;</td><td align='right'>".fprix($row2->montant)."</td>"; 
		$recette += $row2->montant;
	}
 	echo "<td><a href=compta-saisie.php?id=$row2->idtmp&idperiode=$idperiode class=links>Edit</a></td>";

	echo "</tr>";
}
echo "<tr><td colspan=3></td>";
echo "<td align='right'>".fprix($depense)."</td>";
echo "<td align='right'>".fprix($recette)."</td>";
echo "</tr>";
echo "</table>";

	$cnx2=NULL;
}


?>
