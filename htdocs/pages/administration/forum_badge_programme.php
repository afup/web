<?php

require_once dirname(__FILE__) .'/../../../sources/Afup/Bootstrap/Http.php';
// Gestion des droits
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Utils.php';
$droits = AFUP_Utils::fabriqueDroits($bdd);

if (!$droits->estConnecte() ) {
   header('Location: index.php?page=connexion&echec=' . $droits->verifierEchecConnexion());
   exit;
}



require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Inscriptions_Forum.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Facturation_Forum.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Forum.php';

$forum = new AFUP_Forum($bdd);
$forum_inscriptions = new AFUP_Inscriptions_Forum($bdd);
$id_forum = 5;
//$badges =  $forum_inscriptions->obtenirListePourBadges($id_forum);
$badges = array();
$badge_prints =array();
$nb_cols = 3;
$nb_rows = 4;
$badge_row = 1;
$badge_col = 1;
$badge_page = 1;


$programme = $forum->genAgenda('2010',true,true);

$code_salle[8]= "LA1";
$code_salle[9]= "LA2";
$code_salle[10]= "S1";
$code_salle[11]= "S3";
$code_salle[12]= "SAB";
$code_salle[13]= "SCD";
$code_salle[14]= "S2";
//var_dump( $programme);die;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
<style media="all">
td {
  width: 502px;
  height: 576px;
  border: 0px solid red;
  text-align: center;
  font-size: 30px;
  font-family: Arial;
  /*background-color: red;*/
}

.programme td {
  font-size: 12px;
}

table {
  width: 1850px;
  height: 2482px;
  page-break-after: always;
  padding: 50px;
  /*background-color: red;*/
}

th.coin {
  width: 70px;
  height: 20px;
  border: 0px solid blue;
  text-align: center;
  /*background-color: red;*/
}

th {
  font-size: 10px;
  /*background-color: red;*/
}
</style>
</head>

<body style="margin: 0; padding: 0px; ">
<?php foreach(array(1,2) as $nb_page): ?>
<table border="0" cellpadding="0" cellspacing="0" align="center" class="programme">
  <tr>
    <th class="coin" valign="bottom">
    <div style="float: left" align="right"><br />
    __</div>
    <div style="float: right" align="right">|<br />
    <br />
    </div>

    </th>
    <th valign="top">
    <div style="float: right" align="right">|</div>
    </th>
    <th valign="top">
    <div style="float: right" align="right">|</div>
    </th>
    <th valign="top">
    <div style="float: right" align="right">|</div>
    </th>
    <th class="coin" valign="bottom">
    <div style="float: right" align="right"><br />
    __</div>
    </th>
  </tr>
  <?php foreach(array(1,2,3,4) as $row): ?>
  <tr>
    <th valign="bottom" align="left">__</th>
    <?php foreach(array(1,2,3) as $col): ?>
    <?php $nb = 0;?>
    <td>
    <div style="padding:0px 20px;"><?php foreach($programme as $date => $programme_jour): ?>
    <?php 
    //echo $nb_page;
     if ($date == '10-11-2010' && $nb_page == 2) {
         
        continue ;
     }
     if ($date == '09-11-2010' && $nb_page == 1) {
         
        continue ;
     }
    ?>
    <div style="font-size: 15px; font-weight: bold; padding: 3px;"><?php echo $date == '09-11-2010'?'Mardi 09':'Mercredi 10';?>
    Novembre 2010</div>
    <?php foreach($programme_jour as $hour => $session_hours): ?> <?php $nb++;$session = $session_hours[0];?>
    <div style="padding: 3px;background-color:<?php echo $nb % 2 ==0?'white':'#E7E7E7'; ?>;">
    <div
      style="float: left; width: 70px; border: 0px solid; padding: 0px; text-align: center; vertical-align: middle;"><?php echo $hour;?></div>
    <div style="float: right;padding: 0px;border:0px solid;width: 410px;height:<?php echo count($session_hours)*18; ?>px;text-align: left;vertical-align: middle;" >
    <?php foreach($session_hours as $session): ?><span style="width: 50px;font-size: 8px;"><?php echo $code_salle[$session['id_salle']]?> -</span> <?php echo $session['titre']?><br />
    <?php endforeach; ?></div>
    <br style="clear: both;" />
    </div>
    <?php endforeach; ?> <?php endforeach; ?> <br/><i> RDC : SAB (Salle AB) - SCD (Salle CD)<br/> Niveau -1  : LA1 (Amphi 1) - LA2 (Amphi 2) - S1 (Salle 1) - S2 (Salle 2) - S3 (Salle 3)</i></div>
    </td>
    <?php endforeach;?>
    <th valign="bottom" align="right">__</th>
  </tr>
  <?php endforeach; ?>
  <tr>
    <th valign="bottom" class="coin">
    <div style="float: right" align="right">|</div>
    </th>
    <th valign="bottom">
    <div style="float: right" align="right">|</div>
    </th>
    <th valign="bottom">
    <div style="float: right" align="right">|</div>
    </th>
    <th valign="bottom">
    <div style="float: right" align="right">|</div>
    </th>
    <th valign="bottom" class="coin"></th>
  </tr>
</table>
  <?php endforeach; ?>
</body>
</html>
