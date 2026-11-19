<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Talks;

use AppBundle\CFP\PhotoStorage;
use AppBundle\Event\Speaker\PersonJsonLd;
use AppBundle\Event\Speaker\SpeakerProfileResolver;
use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class SpeakerShowAction extends AbstractController
{
    public function __construct(
        private readonly ViewRenderer $view,
        private readonly SpeakerProfileResolver $speakerProfileResolver,
        private readonly PhotoStorage $photoStorage,
        private readonly PersonJsonLd $personJsonLd,
    ) {}

    public function __invoke(Request $request, string $slug): Response
    {
        $profile = $this->speakerProfileResolver->getProfileBySlug($slug);

        if (null === $profile) {
            throw $this->createNotFoundException();
        }

        $photoUrl = null !== $profile->photoSpeaker ? $this->photoStorage->getUrl($profile->photoSpeaker) : null;

        return $this->view->render('site/talks/speaker.html.twig', [
            'profile' => $profile,
            'photo_url' => $photoUrl,
            'person' => $this->personJsonLd->getData(
                $profile,
                $this->generateUrl('talks_speaker', ['slug' => $profile->identity->slug], UrlGeneratorInterface::ABSOLUTE_URL),
                null !== $photoUrl ? $request->getSchemeAndHttpHost() . $photoUrl : null,
            ),
        ]);
    }
}
