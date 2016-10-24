<?php

// Impossible to access the file itself
use Afup\Site\Forum\Inscriptions;
use Afup\Site\Forum\Forum;
use Afup\Site\Utils\Utils;

if (!defined('PAGE_LOADED_USING_INDEX')) {
  trigger_error("Direct access forbidden.", E_USER_ERROR);
  exit;
}

require_once dirname(__FILE__) .'/../../../sources/Afup/Bootstrap/Http.php';
// Gestion des droits

$droits = Utils::fabriqueDroits($bdd);

if (!$droits->estConnecte() ) {
   header('Location: index.php?page=connexion&statut=' . $droits->obtenirStatutConnexion());
   exit;
}







$forum = new Forum($bdd);
$forum_inscriptions = new Inscriptions($bdd);
$id_forum = 7;
//$badges =  $forum_inscriptions->obtenirListePourBadges($id_forum);
$badges = array();
$badge_prints =array();
$nb_cols = 3;
$nb_rows = 4;
$badge_row = 1;
$badge_col = 1;
$badge_page = 1;


$programme = $forum->genAgenda('2012',true,true);

$code_salle[18]= "A";
$code_salle[19]= "H";

//var_dump( $programme);die;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
<style media="all">
@page {
  size: A3;
  margin: 5mm;
}
td {
  width: 502px;
  height: 545px;
  border: 0px solid red;
  text-align: center;
  font-size: 30px;
  font-family: Arial;
  /*background-color: red;*/
}

.programme td {
  font-size: 10px;
}

table {
  width: 1600px;
  height: 2250px;
  page-break-after: always;
  padding: 10px;
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
    <div style="font-size: 15px; font-weight: bold; padding: 3px;"><?php echo $date == '05-06-2012'?'Mardi 05':'Mercredi 06';?>
    Juin 2012</div>
    <?php foreach($programme_jour as $hour => $session_hours): ?> <?php $nb++;$session = $session_hours[0];?>
    <div style="padding: 3px;background-color:<?php echo $nb % 2 ==0?'white':'#E7E7E7'; ?>;-webkit-print-color-adjust: exact;">
    <div
      style="float: left; width: 70px; border: 0px solid; padding: 0px; text-align: center; vertical-align: middle;"><?php echo $hour;?></div>
    <div style="float: right;padding: 0px;border:0px solid;width: 370px;height:<?php echo count($session_hours)*11; ?>px;text-align: left;vertical-align: middle;" >
    <?php foreach($session_hours as $session): ?><span style="width: 50px;font-size: 8px;"><?php echo $code_salle[$session['id_salle']]?> -</span> <?php echo $session['titre']?><br />
    <?php endforeach; ?></div>
    <br style="clear: both;" />
    </div>
    <?php endforeach; ?> <?php endforeach; ?> <br/><i> A :  Amphi Adenauer - H : Salon Honorat</i></div>
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
