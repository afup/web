<?php
require_once ("include/config.inc.php");
?>
<?php

$id=verif_GetPost($_GET['id']);

if (sizeof($_POST) > 0) 
{
 $frm = $_POST;
//$rep=recup_periode($frm[idperiode]);
//$periode=$rep[0];
//$idperiode=$frm[idperiode];   
 
switch($frm[action])
{
	case "Mise A Jour":
	/*	if ( empty($frm['reglement']) ) 
		{
			echo "Le champ est vide";
		} elseif ($_SESSION['ip'] != $_SERVER[REMOTE_ADDR] 
		 		 	|| $_POST['idclef'] != $_SESSION['idclef'])
		{
			echo "Tentative de p�n�tation";
		} else {     */  
		
	/*	
		$sql="UPDATE facture SET 
				`idoperation` = '".$frm[idoperation]."'
				,`montant_theo` = '".htmlentities($frm[montant_theo], ENT_QUOTES)."'
				,`description` = '".htmlentities($frm[description], ENT_QUOTES)."'
				,`idevenement` = '".$frm[idevenement]."'
				,`idcategorie` = '".$frm[idcategorie]."'
				where id='".htmlentities($frm[id])."'  ";
*/
		assert ('$cnx->prepare($sql)');
		$qid=$cnx->prepare($sql);
		$qid->execute();
	//	}
	break;
	
    case "Ajouter":
//    	echo "*";
//$date=DATE("d-m-	Y");
    	//if (isset($_GET['idperiode'])) $idperiode=verif_GetPost($_GET['idperiode']);
//$rep=recup_periode($frm[idperiode]);
//$periode=$rep[0];
//$idperiode=$frm[idperiode];   
		 $sql = "INSERT INTO facture (
		 		`date`
		 		,`numero`
		 		,`societe`
		 		,`adr1`
		 		,`adr2`
		 		,`cp`
		 		,`ville`
		 		,`pays`
		 		,`observation`
		 		)
		 		VALUES (
		 		'".date("Y-m-d")."'
		 		,'".$numero."'
		 		,'".htmlentities($frm[societe], ENT_QUOTES)."'
		 		,'".htmlentities($frm[adr1], ENT_QUOTES)."'
		 		,'".htmlentities($frm[adr2], ENT_QUOTES)."'
		 		,'".htmlentities($frm[cp], ENT_QUOTES)."'
		 		,'".htmlentities($frm[ville], ENT_QUOTES)."'
		 		,'".htmlentities($frm[pays], ENT_QUOTES)."'
		 		,'".htmlentities($frm[observation], ENT_QUOTES)."'
		 		)";
	//	assert ('$cnx->prepare($sql)');
		$qid=$cnx->prepare($sql);
		$qid->execute();
echo $sql."<br>";
//--------------------
$last_id=PDO->lastid;		
		
		 $sql = "INSERT INTO facture_details (
		 		`ref`
		 		,`idfacture`
		 		,`ref`
		 		,`designation`
		 		,`quantite`
		 		,`pu`
		 		)
		 		VALUES (
		 		'".htmlentities($frm[ref][$i], ENT_QUOTES)."'
		 		'".$last_id."'
		 		,'".htmlentities($frm[designation][$i], ENT_QUOTES)."'
		 		,'".htmlentities($frm[quantite][$i], ENT_QUOTES)."'
		 		,'".htmlentities($frm[pu][$i], ENT_QUOTES)."'
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
$sql="SELECT * FROM facture WHERE id='$id' ";	
//assert ('$cnx->prepare($sql)');
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
  <tr> 
    <td valign="top">Nom societe</td>
<td> <input type='text' name='societe'  value='<?php echo $row->societe;?>'></td>
  </tr> 
 <tr> 
    <td valign="top">Adresse</td>
<td> <input type='text' name='adr1'  value='<?php echo $row->adr1;?>'></td>
  </tr> 
 <tr> 
    <td valign="top">adresse 2</td>
<td> <input type='text' name='adr2'  value='<?php echo $row->adr2;?>'></td>
  </tr> 
 <tr> 
    <td valign="top">code postal</td>
<td> <input type='text' name='cp'  value='<?php echo $row->cp;?>'></td>
  </tr> 
 <tr> 
    <td valign="top">Ville</td>
<td> <input type='text' name='ville'  value='<?php echo $row->ville;?>'></td>
  </tr> 
 <tr> 
    <td valign="top">Pays</td>
 <td> <input type='text' name='pays'  value='<?php echo $row->pays;?>'></td>
  </tr> 

  <tr><td colspan=2><hr></td></tr>
  <tr><td colspan=2>
  <table>
  <tr>
  <td>ref</td>
  <td>Designation</td>
  <td>Quantite</td>
  <td>PU</td>
  <td>Montant</td>
  </tr>
 <?php 
for ($i=0;$i<5;$i++)
{ 
	echo "<tr>";
//	echo "<td valign=top>";	
//	echo "</td>";
//   	echo "<td>";
	echo "<td valign=top><input type='text' name='ref[]'  value=''></td>";
	echo "<td><textarea name='designation[]' cols='30' rows='3'>".stripslashes(nl2br($row[designation]))."</textarea></td>";
	echo "<td valign=top><input type='text' name='quantite[]'  value=''></td>";
	echo "<td valign=top><input type='text' name='pu[]'  value=''></td>";
	echo "<td valign=top><input type='text' name='montant[]'  value=''></td>";
//	echo "<input name='iddetails[]' type='hidden' value=".$row[idtmp].">";
//	echo "</td>";
	echo "</tr>";
} 

?>
  
  </table>
<input name="nligne" type="hidden" value="<?php echo $nligne; ?>"> 
  
  
  </td></tr>  
  <tr> 
    <td valign="top">Observation</td>
    <td><textarea name="observation" cols="50" rows="3"><?php echo $row->observation;?></textarea></td>
  </tr>
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
require_once "ifooter.inc.php"; 
?>
</body></html>
