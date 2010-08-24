<?php
require_once ("include/config.inc.php");
?>
<?php

$id=verif_GetPost($_GET['id']);

if (sizeof($_POST) > 0) 
{
 $frm = $_POST;
$rep=recup_periode($frm[idperiode]);
$periode=$rep[0];
$idperiode=$frm[idperiode];   
 
switch($frm[action])
{
	case "Mise A Jour":
	/*	if ( empty($frm['reglement']) ) 
		{
			echo "Le champ est vide";
		} elseif ($_SESSION['ip'] != $_SERVER[REMOTE_ADDR] 
		 		 	|| $_POST['idclef'] != $_SESSION['idclef'])
		{
			echo "Tentative de pénétation";
		} else {     */  
		
		
		$sql="UPDATE budget SET 
				`idoperation` = '".$frm[idoperation]."'
				,`montant_theo` = '".htmlentities($frm[montant_theo], ENT_QUOTES)."'
				,`description` = '".htmlentities($frm[description], ENT_QUOTES)."'
				,`idevenement` = '".$frm[idevenement]."'
				,`idcategorie` = '".$frm[idcategorie]."'
				where id='".htmlentities($frm[id])."'  ";

		assert ('$cnx->prepare($sql)');
		$qid=$cnx->prepare($sql);
		$qid->execute();
	//	}
	break;
	
    case "Ajouter":
//if (isset($_GET['idperiode'])) $idperiode=verif_GetPost($_GET['idperiode']);
//$rep=recup_periode($frm[idperiode]);
//$periode=$rep[0];
//$idperiode=$frm[idperiode];   
		 $sql = "INSERT INTO budget (
		 		`idoperation`
		 		,`idcategorie`
		 		,`montant_theo`
		 		,`description`
		 		,`idevenement`
		 		,`periode`
		 		)
		 		VALUES (
		 		'".$frm[idoperation]."'
		 		,'".$frm[idcategorie]."'
		 		,'".htmlentities($frm[montant_theo], ENT_QUOTES)."'
		 		,'".htmlentities($frm[description], ENT_QUOTES)."'
		 		,'".$frm[idevenement]."'
		 		,'$periode'
		 		)";

		assert ('$cnx->prepare($sql)');
		$qid=$cnx->prepare($sql);
		$qid->execute();
	//}
	break;

}
}

?>
<html>
<head>
<link rel="stylesheet" href="include/css.css" type="text/css">
</head>
<body>
<?php 
require_once "ihead.inc.php"; 
?>

<?php
if ($id)
{
$sql="SELECT * FROM budget WHERE id='$id' ";	
assert ('$cnx->prepare($sql)');
$qid=$cnx->prepare($sql);
$qid->execute();
$row=$qid->fetch(PDO::FETCH_OBJ);
	
}
?>

<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>"  method="post">
	<input type='hidden' name='id'  value='<?php echo $row->id;?>'>
	<input type='hidden' name='idperiode'  value='<?php echo $idperiode;?>'>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
   <tr> 
    <td width=150>Evenement</td>
    <td>
<?php    
$cnx4 = new PDO('mysql:host='.$serveur.';port='.$port.';dbname='.$bdd, $user, $passwd);

$sql="SELECT * FROM budget_evenement ORDER BY evenement";
$even=$cnx4->prepare($sql);
$even->execute();

	echo "<select name=\"idevenement\">";
//	echo ligne_selected(" ","-1",$frm['idrubrique'][$i]);

	while( $row4=$even->fetch(PDO::FETCH_ASSOC) )       
	{
//		echo ligne_selected($row2[nom],$row2[id],$row[idrubrique]);  
		echo ligne_selected($row4[evenement],$row4[id],$row->idevenement);  
	}
	echo "</select>";
	$cnx2=NULL;
?></td>
  </tr>
  <tr><td colspan=2><hr></td></tr>
 
<tr><td>Operation</td><td>
<?php    
$cnx3 = new PDO('mysql:host='.$serveur.';port='.$port.';dbname='.$bdd, $user, $passwd);

$sql="SELECT * FROM budget_operation ORDER BY operation";
$operation=$cnx3->prepare($sql);
$operation->execute();

	echo "<select name=\"idoperation\">";
	echo ligne_selected(" ","-1",$frm['idoperation'][$i]);

	while( $row3=$operation->fetch(PDO::FETCH_ASSOC) )       
	{
		echo ligne_selected($row3[operation],$row3[id],$row->idoperation);  
	}
	echo "</select>";
	$cnx3=NULL;
?>	

</td>
  </tr>
<tr><td>Categorie</td><td>
<?php    
$cnx6 = new PDO('mysql:host='.$serveur.';port='.$port.';dbname='.$bdd, $user, $passwd);

$sql="SELECT * FROM budget_categorie ORDER BY categorie";
$categorie=$cnx6->prepare($sql);
$categorie->execute();

	echo "<select name=\"idcategorie\">";
	echo ligne_selected(" ","-1",$frm['idcategorie'][$i]);

	while( $row6=$categorie->fetch(PDO::FETCH_ASSOC) )       
	{
		echo ligne_selected($row6[categorie],$row6[id],$row->idcategorie);  
	}
 	echo "</select>";
	$cnx6=NULL;
?>	
</td>
  </tr>
  <tr> 
    <td valign="top">Description</td>
    <td><textarea name="description" cols="50" rows="3"><?php echo $row->description;?></textarea></td>
  </tr>
  <tr>
    <td>Montant</td>
    <td><input type='text' name='montant_theo' size='30' value='<?php echo $row->montant_theo;?>'></td>
  </tr>
  <tr><td colspan=2><hr></td></tr>
  <tr>
    <td>&nbsp;</td>
    <td>
    <?php if ($id) { ?>
    	<input type="submit" name="action" value="Mise A Jour">
<?php } else { ?>    	
        <input name="action" type="submit" id="action" value="Ajouter">
   <?php } ?></td>
  </tr>
</table>
</form>
<hr>
<?php
$sql="SELECT budget.*, budget_categorie.categorie, budget_evenement.evenement,budget_operation.operation  
FROM budget_operation INNER JOIN (
		budget_evenement INNER JOIN (
		budget_categorie INNER JOIN budget ON budget_categorie.id = budget.idcategorie) 
		ON budget_evenement.id = budget.idevenement) 
		ON budget_operation.id = budget.idoperation
WHERE budget.periode>='$periode_debut' AND budget.periode<='$periode_fin'  
ORDER BY budget.idoperation, budget_evenement.evenement,budget_categorie.categorie,budget.description  
"; 



assert ('$cnx->prepare($sql)');
$qid=$cnx->prepare($sql);
$qid->execute();


echo "<table width=100% border=1>";
echo "<tr>";
echo "<td>Operation</td><td>Evenement</td><td>Categorie</td><td>Description</td><td>Montant theo</td>";
echo "</tr>";

while( $row=$qid->fetch(PDO::FETCH_OBJ) ) 
{
	echo "<tr>";
	echo "<td>".$row->operation."</td>";
	echo "<td>".$row->evenement."</td>";
	echo "<td>".$row->categorie."</td>";
	echo "<td>".$row->description."</td>";
	echo "<td align='right' width='80'>".number_format($row->montant_theo, 2, ',', ' ')."</td>";
		echo "<td><a href=".$_SERVER['PHP_SELF']."?id=$row->id&idperiode=$idperiode class=links>Edit</a></td>";
		echo "<td>";
		echo "<a ";
		echo "onClick=\"Javascript:return confirm('Êtes-vous sûr de vouloir enlever cette ligne ?');\" ";
		echo "href=".$_SERVER['PHP_SELF']."?id=".$row->id."&choix=trash&idperiode=$idperiode";
		echo " class=links>Supp</a>";
		echo "</td>";
	echo "</tr>";
}
echo "</table>";
?>
<?php 
require_once "ifooter.inc.php"; 
?>
</body></html>