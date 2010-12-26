<?php

class AFUP_Aperos {
    private $_bdd;

    function __construct(&$bdd) {
        $this->_bdd = $bdd;
    }
	
	function ajouter($id_organisateur, $id_ville, $date, $lieu, $etat) {
		$requete = 'INSERT INTO';
		$requete .= '  afup_aperos';
		$requete .= ' SET';
		$requete .= '  id_organisateur = '.(int)$id_organisateur.',';
		$requete .= '  id_ville = '.(int)$id_ville.',';
		$requete .= '  date = '.(int)$date.',';
		$requete .= '  lieu = '.$this->_bdd->echapper($lieu).',';
		$requete .= '  etat = '.(int)$etat;
		
		return $this->_bdd->executer($requete);
	}
	
	function obtenir($id, $champs = '*') {
		$requete = 'SELECT';
		$requete .= '  '.$champs;
		$requete .= ' FROM';
		$requete .= '  afup_aperos';
		$requete .= ' WHERE';
		$requete .= '  id='.(int)$id;
		
		return $this->_bdd->obtenirEnregistrement($requete);
	}
	
	function modifier($id, $id_organisateur, $id_ville, $date, $lieu, $etat) {
		$requete = 'UPDATE';
		$requete .= '  afup_aperos';
		$requete .= ' SET';
		$requete .= '  id_organisateur = '.(int)$id_organisateur.',';
		$requete .= '  id_ville = '.(int)$id_ville.',';
		$requete .= '  date = '.(int)$date.',';
		$requete .= '  lieu = '.$this->_bdd->echapper($lieu).',';
		$requete .= '  etat = '.(int)$etat;
		$requete .= ' WHERE';
		$requete .= '  id = '.(int)$id;
		
		return $this->_bdd->executer($requete);
	}
	
	function supprimer($id) {
		$requete = 'DELETE FROM afup_aperos WHERE id = '.(int)$id;
		return $this->_bdd->executer($requete);
	}
	
	function modifierParticipants($id, $participants = array()) {
		$requete = 'DELETE FROM afup_aperos_participants WHERE id_aperos = '.(int)$id;
		$this->_bdd->executer($requete);
		
		foreach ($participants as $participant) {
			$requete = 'INSERT INTO afup_aperos_participants';
			$requete .= ' SET id_aperos = '.(int)$id.',';
			$requete .= ' id_inscrits = '.(int)$participant;
			$this->_bdd->executer($requete);
		}
	}
	
	function obtenirListeParticipants($id, $ordre = 'afup_aperos_inscrits.pseudo ASC') {
		$requete = 'SELECT';
		$requete .= '  afup_aperos_inscrits.id,';
		$requete .= '  CONCAT(afup_aperos_inscrits.pseudo, " (", afup_aperos_inscrits.nom, " ", afup_aperos_inscrits.prenom, ")") as nom';
		$requete .= ' FROM';
		$requete .= '  afup_aperos_inscrits';
		$requete .= ' INNER JOIN afup_aperos_participants';
		$requete .= '  ON afup_aperos_inscrits.id = afup_aperos_participants.id_inscrits';
		$requete .= '  AND afup_aperos_participants.id_aperos = '.(int)$id;
		$requete .= ' ORDER BY '.$ordre;

		return $this->_bdd->obtenirAssociatif($requete);
	}
	
	function obtenirListe($ordre = 'date_inscription DESC', $associatif = false) {
		$requete = 'SELECT';
		$requete .= '  afup_aperos.*,';
		$requete .= '  afup_aperos_villes.nom as ville,';
		$requete .= '  afup_aperos_inscrits.pseudo as organisateur';
		$requete .= ' FROM';
		$requete .= '  afup_aperos';
		$requete .= ' LEFT JOIN';
		$requete .= '  afup_aperos_villes';
		$requete .= ' ON';
		$requete .= '  afup_aperos_villes.id = afup_aperos.id_ville';
		$requete .= ' LEFT JOIN';
		$requete .= '  afup_aperos_inscrits';
		$requete .= ' ON';
		$requete .= '  afup_aperos_inscrits.id = afup_aperos.id_organisateur';
		$requete .= ' ORDER BY '.$ordre;
		

		if ($associatif) {
			return $this->_bdd->obtenirAssociatif($requete);
		} else {
			return $this->_bdd->obtenirTous($requete);
		}
	}
	
	function obtenirListeEtat() {
		return array(
			-1 => "Inactif",
			0 => "En attente",
			1 => "Actif",
		);
	}
}