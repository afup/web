<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\Blog;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\JsonLd;
use AppBundle\Event\Model\Repository\TalkRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ProgramAction extends AbstractController
{
    public function __construct(
        private readonly JsonLd $jsonLd,
        private readonly EventActionHelper $eventActionHelper,
        private readonly TalkRepository $talkRepository,
    ) {}

    public function __invoke(Request $request, $eventSlug): Response
    {
        $event = $this->eventActionHelper->getEvent($eventSlug);
        $jsonld = $this->jsonLd->getDataForEvent($event);
        $talkAggregates = $this->talkRepository->getByEventWithSpeakers($event, $request->query->getBoolean('apply-publication-date-filters', true));
        $now = new \DateTime();

        return $this->render(
            'blog/program.html.twig',
            [
                'talks' => iterator_to_array($talkAggregates),
                'event' => $event,
                'jsonld' => $jsonld,
                'speakersPagePrefix' => $request->query->get('speakers-page-prefix', '/' . $event->getPath() . '/speakers/'),
                'display_joindin_links' => $now >= $event->getDateStart() && $now <= \DateTimeImmutable::createFromMutable($event->getDateEnd())->modify('+10 days'),
            ],
        );
    }
}
