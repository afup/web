<?php

declare(strict_types=1);

namespace AppBundle\SocialNetwork;

interface Transport
{
    public function socialNetwork(): SocialNetwork;

    /**
     * Cette fonction est responsable des appels aux APIs d'un réseau pour poster un statut.
     */
    public function send(Status $status): ?StatusId;
}
