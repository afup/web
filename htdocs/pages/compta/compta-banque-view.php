<?php 
require_once "include/config.inc.php";
?>
<html>
<head>
<link rel="stylesheet" href="include/css.css" type="text/css">
</head>
<body>
<?php require_once "ihead.inc.php"; ?>
banque
<?php
$compte=verif_GetPost($_GET['compte']);
if ($compte=="banque") $filtreCpte=" AND idevenement!='18' AND idmode_regl!='1' AND idmode_regl!='7' AND idmode_regl!='8' ";
if ($compte=="livret") $filtreCpte=" AND idevenement='18' ";
if ($compte=="espece") $filtreCpte=" AND idmode_regl='1' ";
if ($compte=="paypal") $filtreCpte=" AND idmode_regl='8' ";



//function banque_details()
//{
	echo "<table border='1'>";
echo "<tr><td>Date ecriture</td><td>Operation</td><td>Description</td><td>Depense</td><td>Recette</td><td>Edit</td></tr>";
//$cnx2 = new PDO('mysql:host='.$serveur.';port='.$port.';dbname='.$bdd, $user, $passwd);
$sql="SELECT * 
	   FROM compta 
	   WHERE date_regl>='$periode_debut' AND date_regl<='$periode_fin' AND montant!='0.00'  
		$filtreCpte
	   ORDER BY date_regl";

$qid=$cnx->prepare($sql);
$qid->execute();
while( $row=$qid->fetch(PDO::FETCH_OBJ) ) 
{
	if (substr($row->date_regl,5,2) != $mois_encours)
	{
		$mois_encours=substr($row->date_regl,5,2);
		echo"<tr><td colspan=3></td><td align=right><b>".fprix($sousDepense)."</b></td><td align=right><b>".fprix($sousRecette)."</b></td>";
$dif=$sousRecette-$sousDepense;
$cumul += $dif;
echo "<td>".$cumul."</td>";
echo "</tr>";
		$sousDepense=0;
		$sousRecette=0;
	}
	echo "<tr>";
	echo "<td>".datefr($row->date_regl)."</td>";
	echo "<td>".recup_reglement($row->idmode_regl)."</td>";
	echo "<td>(".$row->obs_regl.") ".$row->description."</td>";
	if ($row->idoperation=="1") 
	{
		echo "<td align='right'>".fprix($row->montant)."</td><td>&nbsp;</td>"; 
		$depense += $row->montant;
		$sousDepense += $row->montant;
	} else { 
		echo "<td>&nbsp;</td><td align='right'>".fprix($row->montant)."</td>"; 
		$recette += $row->montant;
		$sousRecette += $row->montant;
	}
 	echo "<td><a href=compta-saisie.php?id=$row->id&idperiode=$idperiode class=links>Edit</a></td>";

	echo "</tr>";
	
	
}

		echo"<tr><td colspan=3></td><td align=right><b>".fprix($sousDepense)."</b></td><td align=right><b>".fprix($sousRecette)."</b></td>";
echo "</tr>";

echo "<tr><td colspan=3 align='right'>Total</td>";
echo "<td>".fprix($depense)."</td>";
echo "<td>".fprix($recette)."</td>";
echo "</tr>";
echo "<tr><td colspan=3 align='right'>Solde</td>";
$dif=$recette-$depense;
echo "<td colspan=2 align='center'>".fprix($dif)."</td>";
echo "</tr>";
echo "</table>";


//$qid->closeCursor();
$cnx = null;
//}

require_once "ifooter.inc.php";
?>
</body></html>
