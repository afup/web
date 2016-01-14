<?php

// Impossible to access the file itself
if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

// Actions
$defaultAction = 'search';
$action        = verifierAction([
    $defaultAction,
    'results',
]);

// Some smarty vars
$smarty->assign('action', $action);
$smarty->assign('page', $_GET['page']);

// Compta
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Compta.php';
$compta = new AFUP_Compta($bdd);

// Switch on action name
switch ($action) {

    // Search form
    case "search":
    default:
        // nothing to do
        break;

    // Results
    case 'results':
        // No search param?
        if (!isset($_GET['q']) || !($q = trim($_GET['q']))) {
            $smarty->assign('action', $defaultAction);
            continue;
        }

        $smarty->assign('q', htmlspecialchars($q));

        if ($results = $compta->rechercher($q)) {
            $smarty->assign('results', $results);
        } else {
            $smarty->assign('no_results', true);
        }
    break;
}

