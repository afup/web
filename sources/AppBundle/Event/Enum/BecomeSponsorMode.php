<?php

declare(strict_types=1);

namespace AppBundle\Event\Enum;

enum BecomeSponsorMode: string
{
    /** Redirection vers la page de sponsoring du dernier évènement */
    case Event = 'event';
    /** Page de l'association, indépendante de tout évènement */
    case Generic = 'generic';
}
