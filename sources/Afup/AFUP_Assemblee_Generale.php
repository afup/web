<?php

// Voir la classe AFUP_Assemblee_Generale
define('AFUP_ASSEMBLEE_GENERALE_PRESENCE_INDETERMINE', 0);
define('AFUP_ASSEMBLEE_GENERALE_PRESENCE_OUI'        , 1);
define('AFUP_ASSEMBLEE_GENERALE_PRESENCE_NON'        , 2);

class AFUP_Assemblee_Generale
{
    var $_bdd;
    
    function AFUP_Assemblee_Generale(&$bdd)
    {
        $this->_bdd = $bdd;   
    }  
    
    function obternirDerniereDate()
    {
        $requete  = 'SELECT';
        $requete .= '  MAX(date) ';
        $requete .= 'FROM';
        $requete .= '  afup_presences_assemblee_generale ';
        $requete .= 'LIMIT';
        $requete .= '  0, 1 ';
        return $this->_bdd->obtenirUn($requete);
    }
	
    function obtenirListe($date,
                          $ordre      = 'nom')
    {
        $timestamp = convertirDateEnTimestamp($date);
        
        $requete  = 'SELECT';
        $requete .= '  afup_personnes_physiques.id, ';
        $requete .= '  afup_personnes_physiques.email, ';
        $requete .= '  afup_personnes_physiques.login, ';
        $requete .= '  afup_personnes_physiques.nom, ';
        $requete .= '  afup_personnes_physiques.prenom, ';
        $requete .= '  afup_presences_assemblee_generale.date_consultation, ';
        $requete .= '  afup_presences_assemblee_generale.presence, ';
        $requete .= '  afup_personnes_avec_pouvoir.nom as personnes_avec_pouvoir_nom, ';
        $requete .= '  afup_personnes_avec_pouvoir.prenom as personnes_avec_pouvoir_prenom ';
        $requete .= 'FROM';
        $requete .= '  afup_personnes_physiques, ';
        $requete .= '  afup_presences_assemblee_generale ';
        $requete .= 'LEFT JOIN';
        $requete .= '  afup_personnes_physiques as afup_personnes_avec_pouvoir ';
        $requete .= 'ON';
        $requete .= '  afup_personnes_avec_pouvoir.id = afup_presences_assemblee_generale.id_personne_avec_pouvoir ';
        $requete .= 'WHERE';
        $requete .= '  afup_presences_assemblee_generale.date = \'' . $timestamp . '\' ';
        $requete .= 'AND afup_presences_assemblee_generale.id_personne_physique = afup_personnes_physiques.id ';
        $requete .= 'ORDER BY';
        $requete .= '  ' . $ordre . ' ';
        return $this->_bdd->obtenirTous($requete);
    }

    function obtenirPresents($timestamp)
    {
        $requete  = 'SELECT';
        $requete .= '  afup_personnes_physiques.id, ';
        $requete .= '  CONCAT(afup_personnes_physiques.nom, \' \', afup_personnes_physiques.prenom) as nom ';
        $requete .= 'FROM';
        $requete .= '  afup_personnes_physiques, ';
        $requete .= '  afup_presences_assemblee_generale ';
        $requete .= 'WHERE';
        $requete .= '  afup_presences_assemblee_generale.date = \'' . $timestamp . '\' ';
        $requete .= 'AND afup_presences_assemblee_generale.presence = \'1\' ';
        $requete .= 'AND afup_personnes_physiques.id = afup_presences_assemblee_generale.id_personne_physique ';
        $requete .= 'GROUP BY';
        $requete .= '  afup_personnes_physiques.id ';

        return $this->_bdd->obtenirAssociatif($requete);
    }
    
    function obtenirNombreConvocations($timestamp)
    {
        $requete  = 'SELECT';
        $requete .= '  COUNT(*) ';
        $requete .= 'FROM';
        $requete .= '  afup_presences_assemblee_generale ';
        $requete .= 'WHERE';
        $requete .= '  date = \'' . $timestamp . '\' ';
        return $this->_bdd->obtenirUn($requete);
    }

    function obtenirNombrePresencesEtPouvoirs($timestamp)
    {
        $requete  = 'SELECT';
        $requete .= '  COUNT(*) ';
        $requete .= 'FROM';
        $requete .= '  afup_presences_assemblee_generale ';
        $requete .= 'WHERE';
        $requete .= '  afup_presences_assemblee_generale.date = \'' . $timestamp . '\' ';
        $requete .= 'AND';
		$requete .= '   (afup_presences_assemblee_generale.presence = \'1\' ';
        $requete .= ' OR ';
        $requete .= '   afup_presences_assemblee_generale.id_personne_avec_pouvoir > 0) ';
        return $this->_bdd->obtenirUn($requete);
    }

	function obtenirEcartQuorum($timestamp)
	{
		$quorum = ceil($this->obtenirNombreConvocations($timestamp) / 3);
		$ecart = $this->obtenirNombrePresencesEtPouvoirs($timestamp) - $quorum; 
		return $ecart;
	}
    function preparer($date)
    {
        $requete  = 'SELECT';
        $requete .= '  id ';
        $requete .= 'FROM';
        $requete .= '  afup_personnes_physiques ';
        $requete .= 'WHERE etat=1';
        $personnes_physiques = $this->_bdd->obtenirTous($requete);

        $succes = false;
        if (is_array($personnes_physiques)) {
        		$succes = 0;
	        foreach ($personnes_physiques as $personne_physique) {
	            $requete  = 'SELECT';
	            $requete .= '  id ';
	            $requete .= 'FROM';
	            $requete .= '  afup_presences_assemblee_generale ';
	            $requete .= 'WHERE';
	            $requete .= '  id_personne_physique = ' . $personne_physique['id'] . ' ';
	            $requete .= 'AND';
	            $requete .= '  date = ' . mktime(0, 0, 0, $date['m'], $date['d'], $date['Y']);
	            $preparation = $this->_bdd->obtenirUn($requete);
	            if (!$preparation) {
		            $requete  = 'INSERT INTO ';
		            $requete .= '  afup_presences_assemblee_generale (id_personne_physique, date) ';
		            $requete .= 'VALUES (';
		            $requete .= $personne_physique['id']                            . ',';
		            $requete .= mktime(0, 0, 0, $date['m'], $date['d'], $date['Y']) . ')';
			        $succes += $this->_bdd->executer($requete);
	            }
	        }
        }
        return $succes;
        
    }

    function marquerConsultation($login, $timestamp) {
        $requete  = 'UPDATE ';    
        $requete .= '  afup_presences_assemblee_generale, ';
        $requete .= '  afup_personnes_physiques ';
        $requete .= 'SET';
        $requete .= '  afup_presences_assemblee_generale.date_consultation = ' . time() . ' '; 
        $requete .= 'WHERE';
        $requete .= '  afup_presences_assemblee_generale.id_personne_physique = afup_personnes_physiques.id ';
        $requete .= 'AND afup_personnes_physiques.login = ' . $this->_bdd->echapper($login) . ' ';  
        $requete .= 'AND afup_presences_assemblee_generale.date_consultation = \'0\'';  

        return $this->_bdd->executer($requete);    
    }
    
    function preparerCorpsDuMessage($timestamp) {
    	    $corps = "La prochaine assemblée générale de l'AFUP aura lieu le " . date('d/m/Y', $timestamp) . ".\n\n";
    	    $corps .= "Cette AG se tiendra de 20h à 23h dans la salle Madrid de la FIAP ";
    	    $corps .= "(adresse : 30 rue Cabanis - 75014 Paris - métro Glacières).\n\n";

    	    $corps .= "Merci de bien vouloir cliquer sur le lien ci-dessous : ";
    	    $corps .= "il nous sert d'accusé de réception de cette convocation, ";
    	    $corps .= "il vous permet d'indiquer votre présence ";
    	    $corps .= "et - le cas échéant - à qui vous souhaitez transmettre votre pouvoir.\n\n";
    	    
    	    return $corps;
    }
    
    function preparerSujetDuMessage($timestamp) {
    	    $sujet = "AFUP : convocation à l\'assemblée générale du " . date('d/m/Y', $timestamp);
    	    
    	    return $sujet;
    }
    
    function envoyerConvocations($timestamp, $sujet, $corps) {
        $requete  = 'SELECT';
        $requete .= '  afup_personnes_physiques.id, ';
        $requete .= '  afup_personnes_physiques.email, ';
        $requete .= '  afup_personnes_physiques.login, ';
        $requete .= '  CONCAT(afup_personnes_physiques.nom, \' \', afup_personnes_physiques.prenom) as nom ';
        $requete .= 'FROM';
        $requete .= '  afup_personnes_physiques, ';
        $requete .= '  afup_presences_assemblee_generale ';
        $requete .= 'WHERE';
        $requete .= '  afup_presences_assemblee_generale.date = \'' . $timestamp . '\' ';
        $requete .= 'AND afup_personnes_physiques.id = afup_presences_assemblee_generale.id_personne_physique ';
        $requete .= 'AND afup_presences_assemblee_generale.date_consultation = \'0\' ';
        $requete .= 'GROUP BY';
        $requete .= '  afup_personnes_physiques.id ';
        $personnes_physiques = $this->_bdd->obtenirTous($requete);  

        $succes = false;
        require_once 'phpmailer/class.phpmailer.php';
        foreach ($personnes_physiques as $personne_physique) {
            $hash = md5($personne_physique['id'] . '_' . $personne_physique['email'] . '_' . $personne_physique['login']);
            $link = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'] . '?hash='.$hash;

            $mail = new PHPMailer;
            if ($GLOBALS['conf']->obtenir('mails|serveur_smtp')) {
                $mail->IsSMTP();
                $mail->Host = $GLOBALS['conf']->obtenir('mails|serveur_smtp');
                $mail->SMTPAuth = false;
            }
            $mail->AddAddress($personne_physique['email'], $personne_physique['nom']);
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
    
    function modifier($login, $timestamp, $presence, $id_personne_avec_pouvoir)
    {
        $requete  = 'UPDATE ';    
        $requete .= '  afup_presences_assemblee_generale, ';
        $requete .= '  afup_personnes_physiques ';
        $requete .= 'SET';
        $requete .= '  afup_presences_assemblee_generale.presence = ' . $this->_bdd->echapper((int)$presence) . ','; 
        $requete .= '  afup_presences_assemblee_generale.id_personne_avec_pouvoir = ' . $this->_bdd->echapper((int)$id_personne_avec_pouvoir) . ','; 
        $requete .= '  afup_presences_assemblee_generale.date_modification = ' . time() . ' '; 
        $requete .= 'WHERE';
        $requete .= '  afup_presences_assemblee_generale.id_personne_physique = afup_personnes_physiques.id ';
        $requete .= 'AND afup_personnes_physiques.login = ' . $this->_bdd->echapper($login) . ' ';  
        $requete .= 'AND afup_presences_assemblee_generale.date = ' . $timestamp;  

        return $this->_bdd->executer($requete);    
    }
    
    function obtenirInfos($login, $timestamp) {
        $requete  = 'SELECT';
        $requete .= '  afup_presences_assemblee_generale.presence, ';
        $requete .= '  afup_presences_assemblee_generale.id_personne_avec_pouvoir ';
        $requete .= 'FROM';
        $requete .= '  afup_presences_assemblee_generale, ';
        $requete .= '  afup_personnes_physiques ';
        $requete .= 'WHERE';
        $requete .= '  afup_presences_assemblee_generale.id_personne_physique = afup_personnes_physiques.id ';
        $requete .= 'AND afup_personnes_physiques.login = ' . $this->_bdd->echapper($login) . ' ';  
        $requete .= 'AND afup_presences_assemblee_generale.date = ' . $timestamp . ' ';
        $requete .= 'LIMIT 0, 1';

        $infos = $this->_bdd->obtenirEnregistrement($requete, MYSQL_NUM);
        
        return $infos;
    }

}

?>