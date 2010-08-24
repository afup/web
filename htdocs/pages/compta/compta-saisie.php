<?php
require_once ("include/config.inc.php");


if (isset($_GET['id'])) $id=verif_GetPost($_GET['id']); else $id="";
if (isset($_GET['idperiode'])) $idperiode=verif_GetPost($_GET['idperiode']); else $idperiode="";

if (sizeof($_POST) > 0)
{
 $frm = $_POST;

switch($frm[action])
{
        case "Mise A Jour":
        /*        if ( empty($frm['reglement']) )
                {
                        echo "Le champ est vide";
                } elseif ($_SESSION['ip'] != $_SERVER[REMOTE_ADDR]
                                          || $_POST['idclef'] != $_SESSION['idclef'])
                {
                        echo "Tentative de p�n�tation";
                } else {     */
                $sql="UPDATE compta SET
                                `idoperation` = '".$frm[idoperation]."'
                                ,`idcategorie` = '".$frm[idcategorie]."'
                                ,`date_ecriture` = '".$frm[aaaasaisie]."-".$frm[mmsaisie]."-".$frm[jjsaisie]."'
                                ,`montant` = '".htmlentities($frm[montant], ENT_QUOTES)."'
                                ,`description` = '".htmlentities($frm[description], ENT_QUOTES)."'
                                ,`numero` = '".htmlentities($frm[numero], ENT_QUOTES)."'
                                ,`idmode_regl` = '".$frm[idmode_regl]."'
                                ,`date_regl` = '".$frm[aaaaregl]."-".$frm[mmregl]."-".$frm[jjregl]."'
                                ,`idevenement` = '".$frm[idevenement]."'
                                ,`nom_frs` = '".htmlentities($frm[nom_frs], ENT_QUOTES)."'
                                ,`obs_regl` = '".htmlentities($frm[obs_regl], ENT_QUOTES)."'
                                where id='".htmlentities($frm[id])."'  ";
        //        assert ('$cnx->prepare($sql)');
                $qid=$cnx->prepare($sql);
                $qid->execute();
        //        }
        break;

    case "Ajouter":

                 $sql = "INSERT INTO compta (
                                 `idoperation`
                                 ,`idcategorie`
                                 ,`date_ecriture`
                                 ,`montant`
                                 ,`description`
                                 ,`numero`
                                 ,`idmode_regl`
                                 ,`date_regl`
                                 ,`idevenement`
                                 ,`nom_frs`
                                 ,`obs_regl`
                                 )
                                 VALUES (
                                 '".$frm['idoperation']."'
                                 ,'".$frm['idcategorie']."'
                                 ,'".$frm['aaaasaisie']."-".$frm['mmsaisie']."-".$frm['jjsaisie']."'
                                 ,'".htmlentities($frm['montant'], ENT_QUOTES)."'
                                 ,'".htmlentities($frm['description'], ENT_QUOTES)."'
                                 ,'".htmlentities($frm['numero'], ENT_QUOTES)."'
                                 ,'".$frm['idmode_regl']."'
                                 ,'".$frm['aaaaregl']."-".$frm['mmregl']."-".$frm['jjregl']."'
                                 ,'".$frm['idevenement']."'
                                 ,'".$frm['nom_frs']."'
                                 ,'".htmlentities($frm['obs_regl'], ENT_QUOTES)."'
                                 )";

        //        assert ('$cnx->prepare($sql)');
//                $qid=$cnx->prepare($sql);
//                $qid->execute();
                $cnx->beginTransaction();
                $qid=$cnx->prepare($sql);
                $qid->execute();
                $cnx->commit();
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
$sql="SELECT * FROM compta WHERE id='$id' ";
assert ('$cnx->prepare($sql)');
$qid=$cnx->prepare($sql);
$qid->execute();
$row=$qid->fetch(PDO::FETCH_OBJ);

}
?>

<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>"  method="post">
        <input type='text' name='id'  value='<?php echo $row->id;?>'>
        <input type='text' name='idperiode'  value='<?php echo $idperiode;?>'>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width=150>Operation</td>
    <td>
<?php
$cnx2 = new PDO('mysql:host='.$serveur.';port='.$port.';dbname='.$bdd, $user, $passwd);

$sql="SELECT * FROM compta_operation ORDER BY operation";
$journal=$cnx2->prepare($sql);
$journal->execute();

        echo "<select name=\"idoperation\">";
//        echo ligne_selected(" ","-1",$frm['idrubrique'][$i]);

        while( $row2=$journal->fetch(PDO::FETCH_ASSOC) )
        {
//                echo ligne_selected($row2[nom],$row2[id],$row[idrubrique]);
                echo ligne_selected($row2[operation],$row2[id],$row->idoperation);
        }
        echo "</select>";
        $cnx2=NULL;
?>


</td>
  </tr>
   <tr>
    <td width=150>Evenement</td>
    <td>

<?php
$cnx4 = new PDO('mysql:host='.$serveur.';port='.$port.';dbname='.$bdd, $user, $passwd);

$sql="SELECT * FROM compta_evenement ORDER BY evenement";
$even=$cnx4->prepare($sql);
$even->execute();

        echo "<select name=\"idevenement\">";
//        echo ligne_selected(" ","-1",$frm['idrubrique'][$i]);

        while( $row4=$even->fetch(PDO::FETCH_ASSOC) )
        {
//                echo ligne_selected($row2[nom],$row2[id],$row[idrubrique]);
                echo ligne_selected($row4[evenement],$row4[id],$row->idevenement);
        }
        echo "</select>";
        $cnx2=NULL;
?>
</td>
  </tr>
  <tr><td colspan=2><hr></td></tr>
  <tr>
    <td>Operation</td>
    <td>Le <?php
      if ($id)
      {
                  $jjsaisie=substr($row->date_ecriture,8,2);
                $mmsaisie=substr($row->date_ecriture,5,2);
                        $aaaasaisie=substr($row->date_ecriture,0,4);
      } else {
                  $jjsaisie=DATE("d");
                        $mmsaisie=DATE("m");
                        $aaaasaisie=DATE("Y");

      }
          ?>
        <?php

echo "<select name=jjsaisie>";
echo "<option> </option>";

for ($i=1;$i<=31;$i++)
{
echo "<option value=$i ";
if ($jjsaisie==$i) echo " selected ";
echo ">$i</option>";
}
echo "</select>";
?>
        &nbsp;
        <?php
echo "<select name=mmsaisie>";
echo "<option> </option>";
for ($i=1;$i<=12;$i++)
{
echo "<option value=$i ";
if ($mmsaisie==$i) echo " selected ";
echo ">$i</option>";
}

//echo "<option value=$i>$i</option>";
echo "</select>";
?>
        &nbsp;
        <?php
echo "<select name=aaaasaisie>";
echo "<option> </option>";
for ($i=2007;$i<=DATE("Y")+5;$i++)
{
echo "<option value=$i ";
if ($aaaasaisie==$i) echo " selected ";
echo ">$i</option>";
}

//echo "<option value=$i>$i</option>";
echo "</select>";
?>  </td></tr>
<tr><td>  Type</td><td>
<?php
$cnx3 = new PDO('mysql:host='.$serveur.';port='.$port.';dbname='.$bdd, $user, $passwd);

$sql="SELECT * FROM compta_categorie ORDER BY categorie";
$operation=$cnx3->prepare($sql);
$operation->execute();

        echo "<select name=\"idcategorie\">";
        echo ligne_selected(" ","-1",$frm['idcategorie'][$i]);

        while( $row3=$operation->fetch(PDO::FETCH_ASSOC) )
        {
//                echo ligne_selected($row2[nom],$row2[id],$row[idrubrique]);
                echo ligne_selected($row3[categorie],$row3[id],$row->idcategorie);
        }
        echo "</select>";
        $cnx3=NULL;
?>
Nom Fournisseurs &nbsp;<input type='text' name='nom_frs'  value='<?php if (isset($row->nom_frs)) echo stripslashes($row->nom_frs);?>'>

N� &nbsp;<input type="text" name="numero"  value="<?php if (isset($row->numero)) echo stripslashes($row->numero);?>"></td>
  </tr>
  <tr>
    <td valign="top">Description</td>
    <td><textarea name="description" cols="50" rows="3"><?php if (isset($row->description)) echo stripslashes($row->description); ?></textarea></td>
  </tr>
  <tr>
    <td>Montant</td>
    <td><input type='text' name='montant' size='30' value='<?php if (isset($row->montant)) echo stripslashes($row->montant);?>'></td>
  </tr>
  <tr><td colspan=2><hr></td></tr>
  <tr>
    <td>R&egrave;glement</td>
    <td><?php
$cnx3 = new PDO('mysql:host='.$serveur.';port='.$port.';dbname='.$bdd, $user, $passwd);

$sql="SELECT * FROM compta_reglement ORDER BY reglement";
$operation=$cnx3->prepare($sql);
$operation->execute();

        echo "<select name=\"idmode_regl\">";
        echo ligne_selected(" ","-1",$frm['idreglement'][$i]);

        while( $row3=$operation->fetch(PDO::FETCH_ASSOC) )
        {
//                echo ligne_selected($row2[nom],$row2[id],$row[idrubrique]);
                echo ligne_selected($row3[reglement],$row3[id],$row->idmode_regl);
        }
        echo "</select>";
        $cnx3=NULL;
?>  &nbsp;
Le   &nbsp;
<?php
        $jjregl="";
        $mmregl="";
        $aaaaregl="";
      if ($id)
      {
                  $jjregl=substr($row->date_regl,8,2);
                $mmregl=substr($row->date_regl,5,2);
                        $aaaaregl=substr($row->date_regl,0,4);
      } else {
                  $jjsaisie=DATE("d");
                        $mmsaisie=DATE("m");
                        $aaaasaisie=DATE("Y");

      }
          ?>
        <?php

echo "<select name=jjregl>";
echo "<option> </option>";

for ($i=1;$i<=31;$i++)
{
echo "<option value=$i ";
if ($jjregl==$i) echo " selected ";
echo ">$i</option>";
}
echo "</select>";
?>
        &nbsp;
        <?php
echo "<select name=mmregl>";
echo "<option> </option>";
for ($i=1;$i<=12;$i++)
{
echo "<option value=$i ";
if ($mmregl==$i) echo " selected ";
echo ">$i</option>";
}

//echo "<option value=$i>$i</option>";
echo "</select>";
?>
        &nbsp;
        <?php
echo "<select name=aaaaregl>";
echo "<option> </option>";
for ($i=2007;$i<=DATE("Y")+5;$i++)
{
echo "<option value=$i ";
if ($aaaaregl==$i) echo " selected ";
echo ">$i</option>";
}

//echo "<option value=$i>$i</option>";
echo "</select>";
?>
        </td>
  </tr>
  <tr>
    <td>Observation Regl</td>
    <td><input type='text' name='obs_regl' size='30' value='<?php echo stripslashes($row->obs_regl);?>'></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>
    <?php if ($id) { ?>
            <input type="submit" name="action" value="Mise A Jour">
<?php } else { ?>
        <input name="action" type="submit" id="action" value="Ajouter">
   <?php } ?>
      </td>
  </tr>

</table></form>

<?php
require_once "ifooter.inc.php";
?>
</body></html>