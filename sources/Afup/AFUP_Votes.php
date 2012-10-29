<?php

class AFUP_Votes {
	var $_bdd;
    
    function AFUP_Votes(&$bdd) {
        $this->_bdd = $bdd;
    }
    
    function obtenirListePoids($id_vote, $champs = '*', $ordre = 'date DESC', $associatif = false) {
        $requete  = ' SELECT';
        $requete .= ' afup_votes_poids.'.$champs.', ';
        $requete .= ' CONCAT(afup_personnes_physiques.prenom, \' \', afup_personnes_physiques.nom) as personne_physique ';
        $requete .= ' FROM ';
        $requete .= '  afup_votes_poids ';
        $requete .= ' LEFT JOIN ';
        $requete .= '  afup_personnes_physiques ';
        $requete .= ' ON ';
        $requete .= '  afup_personnes_physiques.id = afup_votes_poids.id_personne_physique  ';
        $requete .= ' WHERE ';
        $requete .= '  id_vote= '.(int)$id_vote;
        $requete .= ' ORDER BY ' . $ordre;

        if ($associatif) {
            return $this->_bdd->obtenirAssociatif($requete);
        } else {
            return $this->_bdd->obtenirTous($requete);
        }
    }
    
    function obtenirPoids($id_vote, $id_personne_physique, $champs = '*') {
    	$requete  = ' SELECT ';
    	$requete .= '  ' . $champs . ' ';
    	$requete .= ' FROM ';
    	$requete .= '  afup_votes_poids ';
    	$requete .= ' WHERE id_vote=' . (int)$id_vote;
    	$requete .= ' AND id_personne_physique=' . (int)$id_personne_physique;
    	return $this->_bdd->obtenirEnregistrement($requete);
    }
    
    function voter($id_vote, $id_personne_physique, $commentaire = "", $poids = 0) {
    	$requete  = 'REPLACE INTO ';
    	$requete .= '  afup_votes_poids (id_vote, id_personne_physique, commentaire, poids, date) ';
    	$requete .= 'VALUES (';
    	$requete .= $this->_bdd->echapper($id_vote)              . ',';
    	$requete .= $this->_bdd->echapper($id_personne_physique) . ',';
    	$requete .= $this->_bdd->echapper($commentaire)          . ',';
    	$requete .= $this->_bdd->echapper($poids)                . ',';
    	$requete .= time()                                       . ')';
    	return $this->_bdd->executer($requete);
    }

    function obtenirListeVotesOuverts($timestamp = null, $champs = '*', $ordre = 'jour DESC', $associatif = false) {
    	if (empty($timestamp)) {
    		$timestamp = time();
    	}

        $requete  = ' SELECT';
        $requete .= ' '.$champs.' ';
        $requete .= ' FROM ';
        $requete .= ' afup_votes ';
        $requete .= ' WHERE lancement <= '.$timestamp. ' AND cloture >= '.$timestamp;
        $requete .= ' ORDER BY ' . $ordre;

        if ($associatif) {
            return $this->_bdd->obtenirAssociatif($requete);
        } else {
            return $this->_bdd->obtenirTous($requete);
        }
    }
    
    function obtenirListe($champs = '*', $ordre = 'jour DESC', $associatif = false) {
        $requete  = ' SELECT';
        $requete .= ' '.$champs.' ';
        $requete .= ' FROM ';
        $requete .= ' afup_votes ';
        $requete .= ' ORDER BY ' . $ordre;

        if ($associatif) {
            return $this->_bdd->obtenirAssociatif($requete);
        } else {
            return $this->_bdd->obtenirTous($requete);
        }
    }

    function obtenir($id, $champs = '*') {
        $requete  = 'SELECT';
        $requete .= '  ' . $champs . ' ';
        $requete .= 'FROM';
        $requete .= '  afup_votes ';
        $requete .= 'WHERE id=' . $id;
        return $this->_bdd->obtenirEnregistrement($requete);
    }

    function ajouter($question, $lancement, $cloture, $date) {
        $requete  = 'INSERT INTO ';
        $requete .= '  afup_votes (question, lancement, cloture, date) ';
        $requete .= 'VALUES (';
        $requete .= $this->_bdd->echapper($question)  . ',';
        $requete .= $this->_bdd->echapper($lancement) . ',';
        $requete .= $this->_bdd->echapper($cloture)   . ',';
        $requete .= $this->_bdd->echapper($date)      . ')';
        return $this->_bdd->executer($requete);
    }

    function modifier($id, $question, $lancement, $cloture, $date) {
        $requete  = ' UPDATE ';
        $requete .= '  afup_votes ';
        $requete .= ' SET ';
        $requete .= '  question='  . $this->_bdd->echapper($question)  . ',';
        $requete .= '  lancement=' . $this->_bdd->echapper($lancement) . ',';
        $requete .= '  cloture='   . $this->_bdd->echapper($cloture)   . ',';
        $requete .= '  date='      . $this->_bdd->echapper($date);
        $requete .= ' WHERE ';
        $requete .= '  id=' . $id;
        return $this->_bdd->executer($requete);
    }

    function supprimer($id) {
		$requete = 'DELETE FROM afup_votes WHERE id='.(int)$id;
		return $this->_bdd->executer($requete);
    }
}
