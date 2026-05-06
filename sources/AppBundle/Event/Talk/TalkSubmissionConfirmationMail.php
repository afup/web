<?php

declare(strict_types=1);

namespace AppBundle\Event\Talk;

use AppBundle\Email\Mailer\Mailer;
use AppBundle\Email\Mailer\MailUser;
use AppBundle\Email\Mailer\MailUserFactory;
use AppBundle\Email\Mailer\Message;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Speaker;
use AppBundle\Event\Model\Talk;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class TalkSubmissionConfirmationMail
{
    public function __construct(
        private readonly Mailer $mailer,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly Environment $twig,
        private readonly TranslatorInterface $translator,
    ) {}

    public function send(Talk $talk, Event $event, Speaker $speaker, string $locale = 'fr'): void
    {
        $eventTitle = (string) $event->getTitle();

        if ($locale === 'fr') {
            $firstChar = mb_strtolower(mb_substr($eventTitle, 0, 1));
            $eventParam = in_array($firstChar, ['a', 'e', 'i', 'o', 'u', 'h'], true)
                ? "de l'" . $eventTitle
                : 'du ' . $eventTitle;
        } else {
            $eventParam = $eventTitle;
        }

        $votePageUrl = null;
        if ($event->getVoteEnabled()) {
            $votePageUrl = $this->urlGenerator->generate(
                'vote_index',
                ['eventSlug' => $event->getPath()],
                UrlGeneratorInterface::ABSOLUTE_URL,
            );
        }

        $subject = $this->translator->trans(
            'mail.cfp_submission.subject',
            ['%event%' => $eventTitle, '%title%' => $talk->getTitle()],
            'messages',
            $locale,
        );

        $bodyTitle = $this->translator->trans(
            'mail.cfp_submission.title',
            ['%event%' => $eventParam],
            'messages',
            $locale,
        );

        $content = $this->twig->render('mail_templates/cfp_submission_confirmation_content.html.twig', [
            'talk' => $talk,
            'event' => $event,
            'eventParam' => $eventParam,
            'cfpEndDate' => $event->getDateEndCallForPapers(),
            'votePageUrl' => $votePageUrl,
            'locale' => $locale,
        ]);

        $message = new Message(
            $subject,
            MailUserFactory::conferences(),
            new MailUser($speaker->getEmail(), $speaker->getLabel()),
        );

        $this->mailer->sendTransactional($message, $content, MailUserFactory::conferences()->getEmail(), $bodyTitle);
    }
}
