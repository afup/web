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


if (isset($_GET['tri'])) $tri=verif_GetPost($_GET['tri']); else $tri="";
if (isset($_GET['ordre'])) $ordre=verif_GetPost($_GET['ordre']); else $ordre="";
if ($tri=="date") $order="ORDER BY date_ecriture ";
if ($tri=="regle") $order="ORDER BY date_regl ";

if ($tri && $ordre!="DESC") $ordre = "DESC"; else $ordre = "";

echo "<h2>Depense</h2>";
view(1,$periode_debut,$periode_fin);
echo "<h2>Recette</h2>";
view(2,$periode_debut,$periode_fin);



function view($idoperation,$periode_debut,$periode_fin)
{
GLOBAL $cnx;
Global $idperiode;

$sql="SELECT compta.*,compta.id as idtmp,compta_categorie.id,compta_categorie.categorie   
	FROM compta, compta_categorie  
	WHERE compta.idoperation='$idoperation' AND compta.date_ecriture>='$periode_debut' AND compta.date_ecriture<='$periode_fin' 
		  AND compta.idcategorie = compta_categorie.id     
	ORDER BY compta.date_ecriture";

assert ('$cnx->prepare($sql)');
$qid=$cnx->prepare($sql);
$qid->execute();


echo "<table border=1 width=100%>";
echo "<tr>";
echo "<td>Date</td>";
echo "<td>Evenement";
echo "<td>Categorie</td>";
echo "</td>";
echo "<td>Description</td>";
echo "<td>Montant</td>";
echo "<td>Mode de<br> Reglement";
//echo "<td>Réglé le";
//echo "<a href=".htmlentities($_SERVER['PHP_SELF'])."?tri=regle&ordre=$ordre class=links>Tri</a>";
echo "</td>";
echo "<td>Edit</td>";
echo "</tr>";

while( $row=$qid->fetch(PDO::FETCH_OBJ) ) 
{
	echo "<tr>";
	echo "<td>".datefr($row->date_ecriture)."</td>";
	echo "<td>".recup_evenement($row->idevenement)."</td>";
	echo "<td>".$row->categorie."</td>";
	echo "<td>".$row->description."</td>";
	echo "<td align=right>".fprix($row->montant)."</td>";
	echo "<td>".recup_reglement($row->idmode_regl)."</td>";
	echo "<td><a href=compta-saisie.php?id=$row->idtmp&idperiode=$idperiode class=links>Edit</a></td>";
	echo "</tr>";
}
echo "</table>";

}

$qid->closeCursor();
$cnx = null;

require_once "ifooter.inc.php";
?>
</body></html>