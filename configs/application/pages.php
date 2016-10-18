<?php

use Afup\Site\Association\Personnes_Physiques;

$pages = array(
	'accueil' => array(
		'nom' => 'Accueil',
    	'niveau' => AFUP_DROITS_NIVEAU_MEMBRE,
    ),
    'membre' => array(
        'nom' => 'Mon compte',
        'elements' => array(
		    'membre_coordonnees' => array(
		        'nom' => 'Mes coordonnées',
		        'niveau' => AFUP_DROITS_NIVEAU_MEMBRE,
		    ),
            'membre_wiki' => array(
                'nom' => 'Accès  wiki',
                'niveau' => AFUP_DROITS_NIVEAU_MEMBRE,
                'nouvelle_fenetre' => true,
            ),
		    'membre_redmine' => array(
		        'nom' => 'Déclarer un problème (GitHub)',
		        'niveau' => AFUP_DROITS_NIVEAU_MEMBRE,
                'nouvelle_fenetre' => true,
		    ),
            'membre_cotisation' => array(
                'nom' => 'Ma cotisation',
                'niveau' => AFUP_DROITS_NIVEAU_MEMBRE,
            ),
            'membre_personne_morale' => array(
                'nom' => 'Ma personne morale',
                'niveau' => AFUP_DROITS_NIVEAU_MEMBRE,
            ),
		    'membre_ml' => array(
		        'nom' => 'Listes de diffusion',
		        'niveau' => AFUP_DROITS_NIVEAU_MEMBRE,
		    ),
		    'membre_assemblee_generale' => array(
		        'nom' => 'Présence prochaine AG',
		        'niveau' => AFUP_DROITS_NIVEAU_MEMBRE,
		    ),
		    'membre_votes' => array(
		        'nom' => 'Votes',
		        'niveau' => AFUP_DROITS_NIVEAU_MEMBRE,
		    ),
        	'membre_tags' => array(
		        'nom' => 'Tags',
		        'niveau' => AFUP_DROITS_NIVEAU_MEMBRE,
		    ),
		    'membre_oeuvres' => array(
		        'nom' => 'Oeuvres',
		        'niveau' => AFUP_DROITS_NIVEAU_MEMBRE,
		    ),
            'membres_liste' => array(
                'nom' => 'Annuaire',
                'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
            ),
		),
    ),
    'membres' => array(
        'nom' => 'Membres',
        'elements' => array(
		    'personnes_physiques' => array(
		        'nom' => 'Personnes physiques',
		        'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
		    ),
		    'personnes_morales' => array(
		        'nom' => 'Personnes morales',
				'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
		    ),
		    'relances' => array(
		        'nom' => 'Relances',
		        'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
		    ),
		    'statistiques' => array(
		        'nom' => 'Statistiques',
		        'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
		    ),
		    'mailing' => array(
		        'nom' => 'Mailing',
		        'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
		    ),
		    'assemblee_generale' => array(
		        'nom' => 'Assemblée générale',
		        'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
		    ),
		    'votes' => array(
		        'nom' => 'Votes',
		        'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
		    ),
        ),
    ),
    'site' => array(
        'nom' => 'Site',
        'elements' => array(
		    'site_feuilles' => array(
		        'nom' => 'Feuilles',
		        'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
                'module' => 2,
            ),
		    'site_rubriques' => array(
		        'nom' => 'Rubriques',
		        'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
                'module' => 2,
            ),
		    'site_articles' => array(
		        'nom' => 'Articles',
		        'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
                'module' => 2,
            ),
        ),
    ),
	'antenne' => array(
		'nom' => 'Antennes AFUP',
		'elements' => array(
			'rendez_vous' => array(
				'nom' => 'Rendez-vous',
				'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
                'module' => 4,
			),
			'antenne' => array(
				'nom' => 'Liste des antennes',
				'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
                'module' => 4,
			),
		),
	),
    'forum' => array(
        'nom' => 'Forum',
        'elements' => array(
		    'forum_gestion' => array(
		        'nom' => 'Gestion forum',
		        'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
		    ),
		    'forum_suivi' => array(
		        'nom' => 'Suivi',
		        'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
		    ),
    		'forum_inscriptions' => array(
		        'nom' => 'Inscriptions',
		        'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
		    ),
		    'forum_facturation' => array(
		        'nom' => 'Factures',
		        'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
		    ),
		    'forum_projet' => array(
		        'nom' => 'Projet PHP',
		        'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
    			'module' => 3,
		    ),
		    'forum_sessions' => array(
		        'nom' => 'Sessions',
		        'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
    			'module' => 3,
		    ),
		    'forum_vote_github' => array(
		        'nom' => 'Sessions - votes visiteurs',
		        'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
    			'module' => 3,
		    ),
		    'forum_sessions_notees' => array(
		        'nom' => 'Vote des sessions',
		        'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
    			'module' => 3,
		    ),
		    'forum_conferenciers' => array(
		        'nom' => 'Conférenciers',
		        'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
    			'module' => 3,
		    ),
		    'forum_planning' => array(
		        'nom' => 'Planning',
		        'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
    			'module' => 3,
		    ),
		    'forum_emargement' => array(
		        'nom' => 'Feuille d\'émargement',
		        'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
		    ),
		    'forum_badge' => array(
		        'nom' => 'Badges',
		        'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
		    ),
		    'forum_planning_vote' => array(
		        'nom' => 'Vote Planning',
		        'niveau' => AFUP_DROITS_NIVEAU_MEMBRE,
		    ),
		    'forum_joind_in' => array(
		        'nom' => 'Export sites externes',
		        'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
		    ),
		    'forum_partenaire' => array(
		        'nom' => 'Sponsors/Partenaires',
		        'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
		    ),
		    'forum_accreditation_presse' => array(
		        'nom' => 'Accréditation presse',
		        'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
		    ),
	    ),
    ),
    'annuaire' => array(
        'nom' => 'Annuaire pro',
        'elements' => array(
		    'annuairepro_membres' => array(
		        'nom' => 'Membres',
		        'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
                'module' => 1,
            ),
        ),
    ),
    'tresorerie' => array(
        'nom' => 'Trésorerie',
        'elements' => array(
		    'compta_devis' => array(
		        'nom' => 'Devis - Facture',
		        'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
            ),
    		'compta_journal' => array(
		        'nom' => 'Journal',
		        'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
            ),
		    'compta_banque' => array(
		        'nom' => 'Compte Banques',
		        'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
            ),
		    'compta_synthese' => array(
		        'nom' => 'Synthese Evenement',
		        'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
            ),
		    'compta_balance' => array(
		        'nom' => 'Balance comptable',
		        'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
            ),
		    'compta_bilan' => array(
		        'nom' => 'Bilan',
		        'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
            ),
		    'compta_conf_evenement' => array(
		        'nom' => 'Configuration',
		        'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
            ),
		    'compta_recherche' => array(
		        'nom' => 'Recherche comptable',
		        'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
            ),
        ),
    ),
    'apero' => array(
        'nom' => 'Apéros PHP',
        'elements' => array(
		    'aperos' => array(
		        'nom' => 'Apéros',
		        'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
                'module' => 0,
            ),
		    'aperos_inscrits' => array(
		        'nom' => 'Inscrits',
		        'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
                'module' => 0,
            ),
        ),
    ),
    'planete' => array(
        'nom' => 'Planète PHP FR',
        'elements' => array(
		    'planete_flux' => array(
		        'nom' => 'Flux',
		        'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
		    ),
		    'planete_billet' => array(
		        'nom' => 'Billets',
		        'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
		    ),
        ),
    ),
    'divers' => array(
        'nom' => 'Divers',
        'elements' => array(
		    'configuration' => array(
		        'nom' => 'Configuration',
		        'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
		    ),
            'membre_administrateur' => array(
                'nom' => 'Administrateurs du site',
                'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
            ),
		    'logs' => array(
		        'nom' => 'Logs',
		        'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
		    ),
            'logsvn' => array(
                'nom' => 'Logs SVN',
                'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
            ),
            'loggit' => array(
                'nom' => 'Logs GIT',
                'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
            ),
		    'updatesvn' => array(
		        'nom' => 'Mise à jour via Git',
		        'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
		    ),
		    'info_serveur' => array(
		        'nom' => 'Information serveur',
		        'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
		    ),
        ),
    ),
    'se_deconnecter' => array(
        'nom' => 'Se déconnecter',
        'niveau' => AFUP_DROITS_NIVEAU_MEMBRE,
    ),
    'connexion' => array(
        'nom' => 'Connexion',
    	'masquee' => true,
        'niveau' => AFUP_DROITS_NIVEAU_MEMBRE,
    ),
    'mot_de_passe_perdu' => array(
        'nom' => 'Mot de passe perdu',
    	'masquee' => true,
    	'niveau' => AFUP_DROITS_NIVEAU_MEMBRE,
    ),
    'message' => array(
        'nom' => 'Message',
    	'masquee' => true,
    	'niveau' => AFUP_DROITS_NIVEAU_MEMBRE,
    ),
	'inscription' => array(
	    'nom' => 'Inscription',
		'masquee' => true,
		'niveau' => AFUP_DROITS_NIVEAU_MEMBRE,
	),
	'desinscription_mailing' => array(
	    'nom' => 'Désinscription mailing',
		'masquee' => true,
		'niveau' => AFUP_DROITS_NIVEAU_MEMBRE,
	),
);

$personnes_physiques = new Personnes_Physiques($bdd);

$identifiant = $droits->obtenirIdentifiant();
$personne_physique = $personnes_physiques->obtenir($identifiant);
if ($personne_physique['id_personne_morale'] == 0) {
    // Suppression des pages accessibles aux membres d'une personne morale
    unset($pages['membre']['elements']['membre_personne_morale']);
}


