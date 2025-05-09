<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event;

use AppBundle\Email\Mailer\Mailer;
use AppBundle\Email\Mailer\MailUserFactory;
use AppBundle\Event\Form\SpeakerSuggestionType;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\SpeakerSuggestionRepository;
use AppBundle\Event\Model\SpeakerSuggestion;
use CCMBenchmark\TingBundle\Repository\RepositoryFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SpeakerSuggestionController extends AbstractController
{
    public function __construct(
        private readonly Mailer $mailer,
        private readonly RepositoryFactory $repositoryFactory,
        private readonly EventActionHelper $eventActionHelper,
    ) {
    }

    /**
     * @param string $eventSlug
     *
     * @return Response
     */
    public function index(Request $request, $eventSlug)
    {
        $event = $this->eventActionHelper->getEvent($eventSlug);

        if ($event->getDateEndCallForPapers() < new \DateTime()) {
            return $this->render(
                'event/speaker-suggestion/closed.html.twig',
                [
                    'event' => $event,
                ]
            );
        }

        $form = $this->createForm(SpeakerSuggestionType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $speakerSuggestion = $this->createSpeakerSuggestion($event, $form->getData());

            $this->repositoryFactory
                ->get(SpeakerSuggestionRepository::class)
                ->save($speakerSuggestion)
            ;

            $this->sendMail($event, $speakerSuggestion);

            $this->addFlash('success', 'Merci pour votre suggestion');

            return new RedirectResponse($this->generateUrl('speaker-suggestion', ['eventSlug' => $event->getPath()]));
        }

        return $this->render(
            'event/speaker-suggestion/index.html.twig',
            [
                'form' => $form->createView(),
                'event' => $event,
            ]
        );
    }

    private function createSpeakerSuggestion(Event $event, array $data): SpeakerSuggestion
    {
        return (new SpeakerSuggestion())
            ->setEventId($event->getId())
            ->setSuggesterEmail($data['suggester_email'])
            ->setSuggesterName($data['suggester_name'])
            ->setSpeakerName($data['speaker_name'])
            ->setComment($data['comment'])
            ->setCreatedAt(new \DateTime('now'))
        ;
    }

    private function sendMail(Event $event, SpeakerSuggestion $speakerSuggestion): void
    {
        $subject = sprintf('%s - Nouvelle suggestion de speaker', $event->getTitle());

        $content = $this->renderView(
            'event/speaker-suggestion/mail.txt.twig',
            [
                'event' => $event,
                'speaker_suggestion' => $speakerSuggestion,
            ]
        );

        $this->mailer->sendSimpleMessage($subject, $content, MailUserFactory::conferences());
    }
}
