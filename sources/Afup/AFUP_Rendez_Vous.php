<?php

define('AFUP_RENDEZ_VOUS_REFUSE'           , 0);
define('AFUP_RENDEZ_VOUS_VIENT'            , 1);
define('AFUP_RENDEZ_VOUS_EN_ATTENTE'       , 2);

define('AFUP_RENDEZ_VOUS_CONFIRME'         , 1);
define('AFUP_RENDEZ_VOUS_DECLINE'          , -1);

define('AFUP_RENDEZ_VOUS_COEFF_VIENT'      , 1.1);
define('AFUP_RENDEZ_VOUS_COEFF_EN_ATTENTE' , 1.3);

class AFUP_Rendez_Vous
{
    var $_bdd;
    
    function __construct(&$bdd)
    {
        $this->_bdd = $bdd;   
    }  
    
    function faireVenirInscritsEnAttente($id)
    {
		$places = $this->obtenirPlacesDisponibles($id);
		if ($places > 0 ) {
	        $requete  = ' UPDATE afup_rendezvous_inscrits ';
	        $requete .= ' SET ';
	        $requete .= '  presence = ' . $this->_bdd->echapper(AFUP_RENDEZ_VOUS_VIENT);
	        $requete .= ' WHERE ';
	        $requete .= '  presence = ' . $this->_bdd->echapper(AFUP_RENDEZ_VOUS_EN_ATTENTE);
	        $requete .= ' AND ';
	        $requete .= '  confirme != ' . $this->_bdd->echapper(AFUP_RENDEZ_VOUS_CONFIRME);
	        $requete .= ' AND ';
	        $requete .= '  id_rendezvous = ' . $this->_bdd->echapper($id);
	        $requete .= ' LIMIT ' . $this->_bdd->echapper($places);
	        
	        return $this->_bdd->executer($requete);
		}
		
		return true;
    }

    function refuserInscritsQuiDeclinent($id)
    {
        $requete  = ' UPDATE afup_rendezvous_inscrits ';
        $requete .= ' SET ';
        $requete .= '  presence = ' . $this->_bdd->echapper(AFUP_RENDEZ_VOUS_REFUSE);
        $requete .= ' WHERE ';
        $requete .= '  confirme = ' . $this->_bdd->echapper(AFUP_RENDEZ_VOUS_DECLINE);
        $requete .= ' AND ';
        $requete .= '  id_rendezvous = ' . $this->_bdd->echapper($id);
        
        return $this->_bdd->executer($requete);
    }

    function remplirAvecListeAttente($id)
    {
    	$this->refuserInscritsQuiDeclinent($id);
    	$ok = $this->faireVenirInscritsEnAttente($id);
    	
    	return $ok; 
    }
    
    function preparerCorpsDuMessage($id)
    {
		$champs = $this->obtenir($id);
		$date = date('d/m/Y', $champs['debut']);
		$debut = date('H\hi', $champs['debut']); 
		$fin = date('H\hi', $champs['fin']);
		
		$corps  = "Le prochain rendez-vous AFUP approche.\n\n";
		$corps .= "On y parlera de : " . strip_tags($champs['theme']). ".\n\n";
		$corps .= "Il se tiendra le " . $date . " de ".$debut." à ".$fin.".";
		$corps .= " Pour le lieu il s'agit de : " . strip_tags($champs['lieu']). ").\n\n";
		
		$corps .= "Merci de bien vouloir cliquer sur le lien ci-dessous : ";
		$corps .= "il vous permet de confirmer ou d'infirmer votre présence. ";
		$corps .= "Cette confirmation est fort utile pour les personnes sur liste d'attente.\n\n";
		$corps .= "Merci et à très bientôt !\n";
		$corps .= "    L'équipe AFUP\n\n";
		
		return $corps;
    }
    
    function preparerSujetDuMessage()
    {
	    return "AFUP : demande confirmation pour le prochain rendez-vous";
    }

    function envoyerDemandesConfirmation($id, $sujet, $corps)
    {
        $requete  = 'SELECT';
        $requete .= '  id, ';
        $requete .= '  id_rendezvous, ';
        $requete .= '  nom, ';
        $requete .= '  prenom, ';
        $requete .= '  email ';
        $requete .= 'FROM';
        $requete .= '  afup_rendezvous_inscrits ';
        $requete .= 'WHERE';
        $requete .= '  id_rendezvous = ' . (int)$id . ' ';
        $requete .= 'AND';
        $requete .= '  presence = ' . AFUP_RENDEZ_VOUS_VIENT . ' ';
        $requete .= 'AND';
        $requete .= '  confirme NOT IN ( ' . AFUP_RENDEZ_VOUS_CONFIRME . ', ' . AFUP_RENDEZ_VOUS_DECLINE . ')';
        $requete .= 'GROUP BY';
        $requete .= '  id ';
        $inscrits = $this->_bdd->obtenirTous($requete);  

        $succes = false;
        require_once 'phpmailer/class.phpmailer.php';
        foreach ($inscrits as $inscrit) {
        	$hash=md5(
					utf8_decode(
						$inscrit['id'].$inscrit['id_rendezvous'].$inscrit['nom'].$inscrit['prenom'].$inscrit['email']
					)
				);
            $link = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'] . '?hash='.$hash;
            $link = str_replace('administration/index.php', 'rendezvous/confirmation.php', $link);
            $mail = new PHPMailer;
            if ($GLOBALS['conf']->obtenir('mails|serveur_smtp')) {
                $mail->IsSMTP();
                $mail->Host = $GLOBALS['conf']->obtenir('mails|serveur_smtp');
                $mail->SMTPAuth = false;
            }
            $mail->AddAddress($inscrit['email'], $inscrit['nom']);
            $mail->From     = $GLOBALS['conf']->obtenir('mails|email_expediteur');
            $mail->FromName = $GLOBALS['conf']->obtenir('mails|nom_expediteur');
            $mail->BCC      = $GLOBALS['conf']->obtenir('mails|email_expediteur');
            $mail->Subject  = $sujet;
            $mail->Body     = $corps . $link;

            $mail->Send();
            $succes += 1;
        }
  
        return $succes;    	
    }

    function obtenir($id, $champs = '*')
    {
        $requete = 'SELECT';
        $requete .= '  ' . $champs . ' ';
        $requete .= 'FROM';
        $requete .= '  afup_rendezvous ';
        $requete .= 'WHERE id=' . $id;

        return $this->_bdd->obtenirEnregistrement($requete);
    }

	function supprimerInscrit($id)
	{
        $requete = 'DELETE FROM afup_rendezvous_inscrits WHERE id=' . $id;
        return $this->_bdd->executer($requete);
	}

	function obtenirInscritAConfirmer($hash, $champs = '*')
	{
		$requete  = 'SELECT';
        $requete .= '  ' . $champs . ' ';
        $requete .= ',  CONCAT(id, id_rendezvous, nom, prenom, email)  ';
        $requete .= 'FROM';
        $requete .= '  afup_rendezvous_inscrits ';
        $requete .= 'WHERE';
        $requete .= '  MD5(CONCAT(id, id_rendezvous, nom, prenom, email)) = ' . $this->_bdd->echapper($hash);

        $champs = $this->_bdd->obtenirEnregistrement($requete);
        if (isset($champs['presence']) and $champs['presence'] == AFUP_RENDEZ_VOUS_REFUSE) {
        	return false;
        } else {
        	return $champs;
        }
	}

	function obtenirInscrit($id, $champs = '*')
	{
        $requete = 'SELECT';
        $requete .= '  ' . $champs . ' ';
        $requete .= 'FROM';
        $requete .= '  afup_rendezvous_inscrits ';
        $requete .= 'WHERE id=' . $id;

        return $this->_bdd->obtenirEnregistrement($requete);
	}

	function exporterVersBarCampListeInscritsQuiViennent($id_rendezvous)
	{
	    $liste = $this->obtenirListeInscritsQuiViennent($id_rendezvous);
	    $i = 0;
	    foreach ($liste as &$inscrit) {
	        $inscrit['email'] = str_replace("@", " (at) ", $inscrit['email']);
	        $inscrit['email'] = str_replace(".", " (dot) ", $inscrit['email']);
	        $inscrit['id'] = ++$i;
	    }
	    
	    return $liste;
	}
	
    function obtenirListeInscritsQuiViennent($id_rendezvous)
    {
        $where = ' AND presence IN (' . AFUP_RENDEZ_VOUS_VIENT . ', ' . AFUP_RENDEZ_VOUS_EN_ATTENTE. ') ';
        return $this->obtenirListeInscrits($id_rendezvous, 'creation', $where);    
    }
    
	function obtenirListeInscrits($id_rendezvous,
                                  $ordre='creation',
                                  $extra='')
    {
        $requete  = ' SELECT ';
        $requete .= '  * ';
        $requete .= ' FROM';
        $requete .= '  afup_rendezvous_inscrits ';
        $requete .= ' WHERE ';
        $requete .= '  id_rendezvous = ' . (int)$id_rendezvous;
        $requete .= $extra;
        $requete .= ' ORDER BY ';
        $requete .= '  ' . $ordre . ' ';

        return $this->_bdd->obtenirTous($requete);
    }
    
    function obtenirListe()
    {
    	$requete  = ' SELECT * FROM afup_rendezvous ';
    	//$requete .= ' WHERE debut > '.time();
    	$requete .= ' ORDER BY debut ASC ';
    	
    	return $this->_bdd->obtenirTous($requete);
    }
    
    function obtenirRendezVousFutur($id)
    {
    	$requete  = ' SELECT * FROM afup_rendezvous ';
    	$requete .= ' WHERE debut > '.time();
    	$requete .= ' AND id = ' . (int)$id;
    	$requete .= ' ORDER BY debut ASC ';
    	$requete .= ' LIMIT 0, 1 ';
    	
    	return $this->_bdd->obtenirEnregistrement($requete);
    }
	
	function obtenirRendezVousPasse($id)
    {
    	$requete  = ' SELECT * FROM afup_rendezvous ';
    	$requete .= ' WHERE debut < '.time();
    	$requete .= ' AND id = ' . (int)$id;
    	$requete .= ' ORDER BY debut ASC ';
    	$requete .= ' LIMIT 0, 1 ';
    	
    	return $this->_bdd->obtenirEnregistrement($requete);
    }
    
    function obtenirProchain()
    {
    	$requete  = ' SELECT * FROM afup_rendezvous ';
    	$requete .= ' WHERE debut > '.time();
    	$requete .= ' ORDER BY debut ASC ';
    	$requete .= ' LIMIT 0, 1 ';
    	
    	return $this->_bdd->obtenirEnregistrement($requete);
    }

    function enregistrer($formulaire)
    {
    	//$formulaire->exportValue('grp_inscrition')."<br>";
    	$debut = preg_split("/[:|h]/", (string)$formulaire->exportValue('debut'));
		if (!isset($debut[0])) {
			$debut[0] = 0;
		}
		if (!isset($debut[1])) {
			$debut[1] = 0;
		}
		$fin = preg_split("/[:|h]/", (string)$formulaire->exportValue('fin'));
		if (!isset($fin[0])) {
			$fin[0] = 0;
		}
		if (!isset($fin[1])) {
			$fin[1] = 0;
		}
		$date = $formulaire->exportValue('date');
		$debut = mktime($debut[0], $debut[1], 0, $date['m'], $date['d'], $date['Y']);
		$fin = mktime($fin[0], $fin[1], 0, $date['m'], $date['d'], $date['Y']);
		$inscription=$formulaire->exportValue('inscription');
	
		$id = (int)$formulaire->exportValue('id');
		if ($id > 0) {
	        $requete  = ' UPDATE afup_rendezvous ';
		} else {
	        $requete  = ' INSERT INTO afup_rendezvous ';
		}
        $requete .= ' SET ';
        $requete .= ' titre = '.$this->_bdd->echapper($formulaire->exportValue('titre')) . ',';
        $requete .= ' theme = '.$this->_bdd->echapper($formulaire->exportValue('theme')) . ',';
        $requete .= ' accroche = '.$this->_bdd->echapper($formulaire->exportValue('accroche')) . ',';
        $requete .= ' debut = '.$this->_bdd->echapper($debut) . ',';
        $requete .= ' fin = '.$this->_bdd->echapper($fin) . ',';
        $requete .= ' lieu = '.$this->_bdd->echapper($formulaire->exportValue('lieu')) . ',';
        $requete .= ' adresse = '.$this->_bdd->echapper($formulaire->exportValue('adresse')) . ',';
        $requete .= ' url = '.$this->_bdd->echapper($formulaire->exportValue('url')) . ',';
        $requete .= ' plan = '.$this->_bdd->echapper($formulaire->exportValue('plan')) . ',';
        $requete .= ' id_antenne = '.$this->_bdd->echapper($formulaire->exportValue('id_antenne')) . ', ';
        $requete .= ' inscription = '.$this->_bdd->echapper($inscription[inscription]) . ', ';
        $requete .= ' capacite = '.$this->_bdd->echapper($formulaire->exportValue('capacite'));
        
     	if ($id > 0) {
	        $requete .= ' WHERE id = '.$id;
		}


        return $this->_bdd->executer($requete);
    }

	function obtenirNombreInscritsQuiViennent($id)
	{
		return $this->obtenirNombreInscrits($id, AFUP_RENDEZ_VOUS_VIENT);	
	}
	
	function obtenirNombreInscritsEnAttente($id)
	{
		return $this->obtenirNombreInscrits($id, AFUP_RENDEZ_VOUS_EN_ATTENTE);	
	}

	function obtenirNombreInscrits($id, $presence=false)
	{
		$requete  = ' SELECT COUNT(*) ';
		$requete .= ' FROM afup_rendezvous_inscrits ';
		$requete .= ' WHERE id_rendezvous = ' . (int)$id;
		if ($presence !== false) {
			$requete .= ' AND presence = ' . (int)$presence;
		}

		return $this->_bdd->obtenirUn($requete);
	}

	function obtenirPlacesDisponibles($id)
	{
		$capacite = $this->obtenirCapacite($id);
		$inscrits  = $this->obtenirNombreInscritsQuiViennent($id);
		
		return floor($capacite * AFUP_RENDEZ_VOUS_COEFF_VIENT) - $inscrits;
	}

	function obtenirCapacite($id)
	{
		$requete  = ' SELECT capacite ';
		$requete .= ' FROM afup_rendezvous ';
		$requete .= ' WHERE id = ' . (int)$id;

		return $this->_bdd->obtenirUn($requete);		
	}

	function accepteSurListeAttenteUniquement($id)
	{
		$capacite = $this->obtenirCapacite($id);
		$inscrits  = $this->obtenirNombreInscritsQuiViennent($id);
		$inscrits += $this->obtenirNombreInscritsEnAttente($id);

		if ($inscrits > floor($capacite * AFUP_RENDEZ_VOUS_COEFF_VIENT) 
		and $inscrits <= floor($capacite * AFUP_RENDEZ_VOUS_COEFF_EN_ATTENTE)) {
			return true;
		} else {
			return false;
		}
	}
	
	function estComplet($id)
	{
		return (bool)!$this->obtenirPresencePossible($id);
	}
	function obtenirPresencePossible($id)
	{
		$capacite = $this->obtenirCapacite($id);
		$inscrits  = $this->obtenirNombreInscritsQuiViennent($id);
		$inscrits += $this->obtenirNombreInscritsEnAttente($id);

		if ($inscrits <= floor($capacite * AFUP_RENDEZ_VOUS_COEFF_VIENT)) {
			return AFUP_RENDEZ_VOUS_VIENT;
		} elseif ($inscrits <= floor($capacite * AFUP_RENDEZ_VOUS_COEFF_EN_ATTENTE)) {
			return AFUP_RENDEZ_VOUS_EN_ATTENTE;
		} else {
			return AFUP_RENDEZ_VOUS_REFUSE;
		}
	}

	function enregistrerConfirmationInscrit($formulaire)
	{
		$confirme = $formulaire->exportValue('confirme');
		switch ($confirme) {
			case AFUP_RENDEZ_VOUS_CONFIRME:
				$presence = AFUP_RENDEZ_VOUS_VIENT;
				break;
			case AFUP_RENDEZ_VOUS_DECLINE:
				$presence = AFUP_RENDEZ_VOUS_REFUSE;
				break;
			default:
				$presence = $formulaire->exportValue('presence');
		}

        $requete  = ' UPDATE afup_rendezvous_inscrits ';
        $requete .= ' SET ';
        $requete .= ' id_rendezvous = '.$this->_bdd->echapper($formulaire->exportValue('id_rendezvous')) . ',';
        $requete .= ' nom = '.$this->_bdd->echapper($formulaire->exportValue('nom')) . ',';
        $requete .= ' prenom = '.$this->_bdd->echapper($formulaire->exportValue('prenom')) . ',';
        $requete .= ' entreprise = '.$this->_bdd->echapper($formulaire->exportValue('entreprise')) . ',';
        $requete .= ' email = '.$this->_bdd->echapper($formulaire->exportValue('email')) . ',';
        $requete .= ' telephone = '.$this->_bdd->echapper($formulaire->exportValue('telephone')) . ',';
        $requete .= ' presence = '.$this->_bdd->echapper($presence) . ',';
        $requete .= ' confirme = '.$this->_bdd->echapper($confirme);
        $requete .= ' WHERE id = '.$this->_bdd->echapper($formulaire->exportValue('id'));

        return $this->_bdd->executer($requete);
	}

    function enregistrerInscrit($formulaire)
    {
		$id_rendezvous = (int)$formulaire->exportValue('id_rendezvous');
		if ($id_rendezvous <= 0) {
			return false;
		}
		
		$id = $formulaire->exportValue('id');
		if ($id > 0) {
	        $presence = $formulaire->exportValue('presence');
	        $creation = $formulaire->exportValue('creation');

	        $requete  = ' UPDATE afup_rendezvous_inscrits ';
		} else {
	        $presence = $this->obtenirPresencePossible($id_rendezvous);
	        $creation = time();

	        $requete  = ' INSERT INTO afup_rendezvous_inscrits ';
		}
        $requete .= ' SET ';
        $requete .= ' id_rendezvous = '.$this->_bdd->echapper($formulaire->exportValue('id_rendezvous')) . ',';
        $requete .= ' nom = '.$this->_bdd->echapper($formulaire->exportValue('nom')) . ',';
        $requete .= ' prenom = '.$this->_bdd->echapper($formulaire->exportValue('prenom')) . ','; 
        $requete .= ' entreprise = '.$this->_bdd->echapper($formulaire->exportValue('entreprise')) . ',';
        $requete .= ' email = '.$this->_bdd->echapper($formulaire->exportValue('email')) . ',';
        $requete .= ' telephone = '.$this->_bdd->echapper($formulaire->exportValue('telephone')) . ',';
        $requete .= ' presence = '.$this->_bdd->echapper($presence) . ',';
        $requete .= ' confirme = '.$this->_bdd->echapper($formulaire->exportValue('confirme')) . ',';
        $requete .= ' creation = '.$this->_bdd->echapper($creation);
		if ($id > 0) {
	        $requete .= ' WHERE id = '.$this->_bdd->echapper($id);
		}

        return $this->_bdd->executer($requete);
    }


    function obtenirListAntennes($filtre='',$where='')
    {
    	$requete  = 'SELECT ';
    	$requete .= 'id, ville ';
    	$requete .= 'FROM  ';
    	$requete .= 'afup_antenne  ';
    	if ($where)		$requete .= 'WHERE id=' . $where. ' ';
    
    	$requete .= 'ORDER BY ';
    	$requete .= 'ville ';
    
    	if ($where) {
    		return $this->_bdd->obtenirEnregistrement($requete);
    	}elseif ($filtre)	{
    		return $this->_bdd->obtenirTous($requete);
    	} else {
    		$data=$this->_bdd->obtenirTous($requete);
    		$result[]="";
    		foreach ($data as $row)
    		{
    			$result[$row['id']]=$row['ville'];
    		}
    
    		return $result;
    	}
    }
}

?>