<?php

declare(strict_types=1);

use AppBundle\Association\Form\HTML_QuickForm;

/**
 * Affiche un message puis redirige le visiteur vers une URL spécifiée
 * @param   string  $message    Message à afficher
 * @param   string  $url        URL vers laquelle rediriger le visiteur
 * @param   bool    $erreur     S'agit-il d'une erreur
 */
function afficherMessage($message, string $url, $erreur = false): void
{
    $_SESSION['flash']['message'] = $message;
    $_SESSION['flash']['erreur'] = $erreur;
    header('Location: ' . $url);
    exit;
}

/**
 * Renvoit l'instance d'un formulaire
 *
 * Un formulaire est instancié, configuré puis renvoyé
 *
 * @param   string  $url        URL pour l'attribut "action" du formulaire
 * @param   string  $nom        Nom du formulaire
 */
function instancierFormulaire($url = null, $nom = 'formulaire'): HTML_QuickForm
{
    if (is_null($url)) {
        $url = $_SERVER['REQUEST_URI'];
    }
    $formulaire = new HTML_QuickForm($nom, 'post', $url);
    $formulaire->removeAttribute('name');
    return $formulaire;
}

/**
 * Renvoit un tableau contenant les éléments d'un formulaire
 *
 * @param HTML_QuickForm $formulaire Formulaire à traiter
 * @return  array
 */
function genererFormulaire(HTML_QuickForm &$formulaire)
{
    foreach ($formulaire->getElements() as $el) {
        $attrs = $el->getAttributes();
        if ($el instanceof HTML_QuickForm_submit) {
            $attrs['class'] = 'ui primary button';
        }
        $el->setAttributes($attrs);
    }

    $renderer = new HTML_QuickForm_Renderer_Array(true, true);
    $formulaire->accept($renderer);
    $resultat = $renderer->toArray();
    unset($renderer);
    return $resultat;
}

/**
 * Vérifie qu'une action est disponible et si ce n'est pas le cas, renvoit l'action par défaut
 *
 * L'action par défaut est la première des actions disponibles.
 *
 * @param   array  $actions_disponibles    Actions disponibles
 * @return  string
 */
function verifierAction($actions_disponibles)
{
    if (!is_array($actions_disponibles) || count($actions_disponibles) == 0) {
        trigger_error("Les actions disponibles doivent être passées sous forme d'un tableau d'au moins un élément", E_USER_ERROR);
        return false;
    }

    if (!empty($_GET['action']) && in_array($_GET['action'], $actions_disponibles)) {
        return $_GET['action'];
    } else {
        return $actions_disponibles[0];
    }
}

/*
 * Remplace une caractère accentué par sa version non accentuée
 *
 * @param   string  $texte  Texte à traiter
 * @return  string          Texte traité
 */
function supprimerAccents($texte): ?string
{
    $texte = htmlentities((string) $texte);
    return preg_replace('/&([a-z])[a-z]+;/i',"$1", $texte);
}

function obtenirTitre($pages, $page)
{
    foreach ($pages as $_page => $_page_details) {
        if ($page == $_page) {
            return $_page_details['nom'];
        }
        if (isset($_page_details['elements']) && is_array($_page_details['elements'])) {
            foreach ($_page_details['elements'] as $_element => $_element_details) {
                if ($page == $_element) {
                    return $_element_details['nom'];
                }
            }
        }
    }
    return null;
}
