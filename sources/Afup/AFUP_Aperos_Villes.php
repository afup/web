<?php
class AFUP_Aperos_Villes {
    private $_bdd;

    function __construct(&$bdd) {
        $this->_bdd = $bdd;
    }
	
//	function ajouter($pseudo, $mot_de_passe, $nom, $prenom, $email, $site_web, $id_ville, $date_inscription, $validation) {
//		$requete = 'INSERT INTO';
//		$requete .= '  afup_aperos_inscrits';
//		$requete .= ' SET';
//		$requete .= '  pseudo = '.$this->_bdd->echapper($pseudo).',';
//		$requete .= '  mot_de_passe = '.$this->_bdd->echapper($this->crypter($mot_de_passe)).',';
//		$requete .= '  nom = '.$this->_bdd->echapper($nom).',';
//		$requete .= '  prenom = '.$this->_bdd->echapper($prenom).',';
//		$requete .= '  email = '.$this->_bdd->echapper($email).',';
//		$requete .= '  site_web = '.$this->_bdd->echapper($site_web).',';
//		$requete .= '  id_ville = '.(int)$id_ville.',';
//		$requete .= '  date_inscription = '.(int)$date_inscription.',';
//		$requete .= '  validation = '.(int)$validation;
//		
//		return $this->_bdd->executer($requete);
//	}
//	
//	function obtenir($id, $champs = '*') {
//		$requete = 'SELECT';
//		$requete .= '  '.$champs;
//		$requete .= ' FROM';
//		$requete .= '  afup_aperos_inscrits';
//		$requete .= ' WHERE';
//		$requete .= '  id='.(int)$id;
//		
//		return $this->_bdd->obtenirEnregistrement($requete);
//	}
//	
//	function modifier($id, $pseudo, $mot_de_passe, $nom, $prenom, $email, $site_web, $id_ville, $validation) {
//		$requete = 'UPDATE';
//		$requete .= '  afup_aperos_inscrits';
//		$requete .= ' SET';
//		$requete .= '  pseudo = '.$this->_bdd->echapper($pseudo).',';
//		if (!empty($mot_de_passe)) {
//			$requete .= '  mot_de_passe = '.$this->_bdd->echapper($this->crypter($mot_de_passe)).',';
//		}
//		$requete .= '  nom = '.$this->_bdd->echapper($nom).',';
//		$requete .= '  prenom = '.$this->_bdd->echapper($prenom).',';
//		$requete .= '  email = '.$this->_bdd->echapper($email).',';
//		$requete .= '  site_web = '.$this->_bdd->echapper($site_web).',';
//		$requete .= '  id_ville = '.(int)$id_ville.',';
//		$requete .= '  date_inscription = '.(int)$date_inscription.',';
//		$requete .= '  etat = '.(int)$validation;
//		$requete .= ' WHERE';
//		$requete .= '  id = '.(int)$id;
//		
//		return $this->_bdd->executer($requete);
//	}
//	
//	function supprimer($id) {
//		$requete = 'DELETE FROM afup_aperos_inscrits WHERE id = '.(int)$id;
//		return $this->_bdd->executer($requete);
//	}
//	
	function obtenirListe($ordre = 'nom ASC', $associatif = false) {
		$requete = 'SELECT';
		$requete .= '  *';
		$requete .= ' FROM';
		$requete .= '  afup_aperos_villes';
		$requete .= ' ORDER BY '.$ordre;

		if ($associatif) {
			return $this->_bdd->obtenirAssociatif($requete);
		} else {
			return $this->_bdd->obtenirTous($requete);
		}
	}
}