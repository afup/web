<?php

declare(strict_types=1);
namespace Afup\Site;

interface AuthentificationInterface
{
    /**
     * Connecte l'utilisateur
     *
     * @param  Array $event Evenement de connection
     */
    public function seConnecter($event): void;

    /**
     * Deconnecte l'utilisateur
     *
     * @param  Array $event Evenement de déconnection
     */
    public function seDeconnecter($event): void;
}
