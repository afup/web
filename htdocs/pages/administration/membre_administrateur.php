<?php

declare(strict_types=1);

// Impossible to access the file itself
use AppBundle\Controller\LegacyController;

/** @var LegacyController $this */
if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$userRepository = $this->userRepository;

$action = verifierAction(['lister', 'detail', 'rechercher']);
$smarty->assign('action', $action);

$administrateurs = [];
foreach ($userRepository->getAdministrators() as $admin) {
    $administrateurs[] = [
        'id' => $admin->getId(),
        'nom' => $admin->getLastname(),
        'prenom' => $admin->getFirstName(),
        'etat' => $admin->getStatus(),
        'niveau' => $admin->getLevel(),
        'niveau_annuaire' => $admin->getDirectoryLevel(),
        'niveau_site' => $admin->getWebsiteLevel(),
        'niveau_forum' => $admin->getEventLevel(),
        'niveau_antenne' => $admin->getOfficeLevel(),
    ];
}

$smarty->assign('administrateurs', $administrateurs);
