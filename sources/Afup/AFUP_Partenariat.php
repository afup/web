<?php

class AFUP_Partenariat {
	public $_bdd;

	function __construct(&$bdd) {
		$this->_bdd = $bdd;
	}
	
	function verifierMembre($nom = "", $prenom = "", $email = "") {
		$filtres = array();
		if (!empty($nom)) {
			$filtres[] = 'nom LIKE "%'.$nom.'%"';
			if (!empty($prenom)) {
				$filtres[] = 'prenom LIKE "%'.$prenom.'%"';
			}
		}
		if (!empty($email)) {
			$filtres[] = 'email LIKE "%'.$email.'%"';
		}
		
		if (count($filtres) > 0) {
			$personnes = $this->_bdd->obtenirTous('
				SELECT afup_personnes_physiques.*
				FROM afup_personnes_physiques
				WHERE '.join(' AND ', $filtres)
			);
			
			if (count($personnes) > 0) {
				return $this->afficherPersonnesPhysiques($personnes);
			}
		}

		return $this->afficherEchec();
	}
	
	function afficherPersonnesPhysiques($personnes) {
		$resultat = '<p>Bingo, nous avons trouvé :</p>';
		
		$resultat .= '<ul>';
		foreach ($personnes as $personne) {
			$resultat .= '<li>'.$this->afficherPersonnePhysique($personne).'</li>';
		}
		$resultat .= '</ul>';
		
		return $resultat;
	}
	
	function afficherPersonnePhysique($personne) {
		$resultat = $personne['prenom'].' '.$personne['nom'].' à '.$personne['ville'];
		
		if ($personne['etat'] > 0) {
			$resultat .= ' (il est bien membre actif).';
		} else {
			$resultat .= ' (désolé il n\'est plus membre).';
		}
		
		return $resultat;
	}
	
	function afficherEchec() {
		return '<p>Désolé aucun membre ne correspond à votre recherche.</p>';
	}
}
