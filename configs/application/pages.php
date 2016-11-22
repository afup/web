<?php
/*

if (isset($_GET['page']) && $_GET['page'] === 'connexion') {
    $pages = [
        'connexion' => array(
            'nom' => 'Connexion',
            'masquee' => true,
            'niveau' => AFUP_DROITS_NIVEAU_MEMBRE,
        ),
    ];
    return;
}


$personnes_physiques = new Personnes_Physiques($bdd);

$identifiant = $droits->obtenirIdentifiant();
$personne_physique = $personnes_physiques->obtenir($identifiant);
*/
/*if ($personne_physique['id_personne_morale'] == 0) {
    // Suppression des pages accessibles aux membres d'une personne morale
    unset($pages['membre']['elements']['membre_personne_morale']);
}*/

/**
 * @var $roles \Symfony\Component\Security\Core\Role\Role[]
 */
/*$checkPages = function (&$pages) use ($roles, &$checkPages) {
    foreach ($pages as $id => $page) {
        if (isset($page['elements'])) {
            $checkPages($page['elements']);
        } elseif(isset($page['role'])) {
            $include = false;
            foreach ($roles as $role) {
                if ($role->getRole() === $role) {
                    $include = true;
                    continue;
                }
            }
            if ($include === false) {
                unset($pages[$id]);
            }
        }
    }
};

$checkPages($pages);
*/