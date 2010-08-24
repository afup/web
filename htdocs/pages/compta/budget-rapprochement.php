<?php 
require_once "include/config.inc.php";
?>
<html>
<head></head>
<body>
<?php require_once "ihead.inc.php"; ?>
en cours

<?php
exit;
/*
$tri=verif_GetPost($_GET['tri']);;
$ordre=verif_GetPost($_GET['ordre']);;
if ($tri=="date") $order="ORDER BY date_ecriture ";
if ($tri=="regle") $order="ORDER BY date_regl ";

if ($tri && $ordre!="DESC") $ordre = "DESC"; else $ordre = "";
*/
//$start=verif_GetPost($_GET['start']);;
//if(!$start)
//    $start=0;
//else 
//	$start = $start;

/*$sql="select a.*,b.journal,c.operation,d.evenement,e.reglement 
	 FROM compta_ecriture a,compta_journal b,compta_operation c,compta_evenement d,compta_reglement e 
	 WHERE a.idjournal=b.id 
	 	AND a.idoperation=c.id 
	 	AND a.idmode_regl=e.id
	 	AND a.idevenement=d.id
	 	$filtre
	 	$order $ordre
	 ";
*/
echo "Rapprochement budget/compta";
//liste budget et liste compta

$sql="
SELECT compta_ecriture.description, compta_ecriture.idevenement, budget_rappro.idcompta,budget_rappro.idbudget
FROM compta_ecriture LEFT JOIN budget_rappro ON compta_ecriture.id = budget_rappro.idcompta
WHERE compta_ecriture.idevenement='1'
 ";
//assert ('$cnx->prepare($sql)');
$qid=$cnx->prepare($sql);
$qid->execute();

echo "<table border=1 width=100%>";
echo "<tr><td>Compta</td><td>Budget</td></tr>";
while( $row=$qid->fetch(PDO::FETCH_OBJ) ) 
{
	echo "<tr>";
	echo "<td>".$row->description."</td>";
	echo "<td>";
	//echo "<select>";

$cnx2 = new PDO('mysql:host='.$serveur.';port='.$port.';dbname='.$bdd, $user, $passwd);
$sqlBudget="SELECT budget.*,budget.id as idtmp, budget_categorie.categorie, budget_evenement.evenement 
FROM budget_evenement INNER JOIN (budget_categorie INNER JOIN budget ON budget_categorie.id = budget.idcategorie) ON budget_evenement.id = budget.idevenement

ORDER BY budget.idoperation, budget_categorie.categorie,budget.description  
"; 	
echo $sqlBudget."<br>";
//WHERE (((budget.idoperation)=1)) 

$qid2=$cnx2->prepare($sqlBudget);
$qid2->execute();


	echo "<select name=\"budge[]\">";
	echo ligne_selected(" ","-1",$frm['idrubrique'][$i]);

	while( $row2=$qid2->fetch(PDO::FETCH_ASSOC) )       

//while( $row2=$qid->fetch(PDO::FETCH_OBJ) ) 
{
//	echo "<option>". $row2->budget_categorie.categorie."-".$row2->budget_evenement.evenement."</option>";
//echo  $row2->budget_categorie.categorie."-".$row2->budget_evenement.evenement."<br>";
	echo ligne_selected($row2['categorie'],$row2[idtmp],$row['idcompta']);  

//	echo "<option value='".$row2->idtmp."'";
//	//if ($value==$entree) $resultat .= " SELECTED ";
//	echo ">".$row2['categorie']."</option>";

//echo $row2[categorie]."-".$row2[evenement]."<br>";

}
//$cnx2 = null;
//	echo "</select>";
	echo "</td>";
	echo "</tr>";
//$depense+=$row->montant_theo;
}
echo "</table>";


$qid->closeCursor();
$cnx = null;


require_once "ifooter.inc.php";
?>
</body></html>