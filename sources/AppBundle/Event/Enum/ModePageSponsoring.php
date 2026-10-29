<?php

declare(strict_types=1);

namespace AppBundle\Event\Enum;

enum ModePageSponsoring: string
{
    /** Redirection vers la page de sponsoring du dernier évènement */
    case Evenement = 'evenement';
    /** Page de l'association, indépendante de tout évènement */
    case Generique = 'generique';
}
