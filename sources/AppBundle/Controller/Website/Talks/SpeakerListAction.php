<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Talks;

use AppBundle\Event\Speaker\SpeakerProfileResolver;
use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class SpeakerListAction extends AbstractController
{
    public function __construct(
        private readonly ViewRenderer $view,
        private readonly SpeakerProfileResolver $speakerProfileResolver,
    ) {}

    public function __invoke(): Response
    {
        // Les identités sont déjà triées par nom puis prénom : les groupes sortent dans l'ordre alphabétique.
        $speakersByInitial = [];
        foreach ($this->speakerProfileResolver->getIdentities() as $identity) {
            $speakersByInitial[$identity->getInitial()][] = $identity;
        }

        return $this->view->render('site/talks/speakers.html.twig', [
            'speakers_by_initial' => $speakersByInitial,
        ]);
    }
}
