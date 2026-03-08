<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\Event;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class CalendarAction extends AbstractController
{
    public function __invoke(string $eventSlug): Response
    {
        return $this->redirect('https://event.afup.org');
    }
}
