<?php

declare(strict_types=1);
namespace Afup\Site;

/**
 * Gestion de l'authentification pour le Wiki de l'afup
 */
class AuthentificationWiki implements AuthentificationInterface
{
    /**
     * Connecte l'utilisateur
     *
     * @param  Array $event Evenement de connection
     */
    public function seConnecter($event): void
    {
        $wikiUser = [];
        $wikiUser["show_comments"] = "Y";
        $wikiUser["name"] = ucfirst(strtolower((string) $event["prenom"])) . ucfirst(strtolower((string) $event["nom"]));
        $wikiUser["email"] = $event["email"];
        $_SESSION["user"] = $wikiUser;
    }

    /**
     * Déconnecte l'utilisateur
     *
     * @param  Array $event Evenement de déconnection
     */
    public function seDeconnecter($event): void
    {
        unset($_SESSION["user"]);
    }
}
