<?php


function fprix(&$prix_nok) 
{

$prix_ok = number_format($prix_nok,2, ',', ' ');

return $prix_ok;

}

function ligne_selected($nom,$value,$entree)
{
	$resultat="<option value='".$value."' ";
	if ($value==$entree) $resultat .= " SELECTED ";
	$resultat .= ">".$nom."</option>";
	return $resultat;
}   
 
function verif_GetPost($clef='')
{
	
	if ( empty($clef) && isset($clef) ) 
		return false;
	elseif (strlen($clef)<1)
		return false;

	$clef=htmlentities($clef, ENT_QUOTES,'UTF-8');
	return $clef;
	
}
function datefr($ladate)
{

setlocale (LC_TIME, "fr_FR");
$date = strftime("%d-%m-%Y",strtotime($ladate));

return $date;

}

function recup_evenement($evenement='')
{
GLOBAL $cnx;

$sql="SELECT * FROM compta_evenement WHERE id='$evenement' ORDER BY evenement";

$qid=$cnx->prepare($sql);
$qid->execute();
$row=$qid->fetch(PDO::FETCH_ASSOC) ;

return $row[evenement];

	
}
function recup_operation($idoperation)
{
GLOBAL $cnx;

$sql="SELECT * FROM compta_operation WHERE id='$idoperation' ORDER BY operation";

$qid=$cnx->prepare($sql);
$qid->execute();
$row=$qid->fetch(PDO::FETCH_ASSOC) ;

return $row[operation];
 $cnx=NULL;
	
}
function recup_reglement($id)
{
GLOBAL $cnx;

$sql="SELECT * FROM compta_reglement WHERE id='$id' ORDER BY reglement";

$qid=$cnx->prepare($sql);
$qid->execute();
$row=$qid->fetch(PDO::FETCH_ASSOC) ;

return $row[reglement];
 $cnx=NULL;
	
}

function recup_periode($id)
{
GLOBAL $cnx;
$sql="SELECT date_debut,date_fin FROM compta_periode WHERE id='$id'";
$qid=$cnx->prepare($sql);
$qid->execute();
$row=$qid->fetch(PDO::FETCH_ASSOC) ;

$tableau[]=$row['date_debut'];
$tableau[]=$row['date_fin'];
return $tableau;

$cnx=NULL;
	
}

function recup_budget_evenement($evenement='')
{
GLOBAL $cnx;

$sql="SELECT * FROM budget_evenement WHERE id='$evenement' ORDER BY evenement";

$qid=$cnx->prepare($sql);
$qid->execute();
$row=$qid->fetch(PDO::FETCH_ASSOC) ;

return $row[evenement];
}

?>
