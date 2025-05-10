<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

/**
 * Cette interface permet d'identifier les controllers ayant un sélecteur d'évènement.
 * Elle sert à faire une redirection si l'id de l'évènement n'est pas présent dans l'url.
 *
 * @see RedirectEventFromSessionListener
 */
interface AdminActionWithEventSelector
{
    public const SESSION_KEY = 'event_selector_current_id';
}
