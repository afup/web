<?php

declare(strict_types=1);

namespace AppBundle\Event\Talk;

use AppBundle\Email\Mailer\Mailer;
use AppBundle\Email\Mailer\MailUser;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\GithubUser;
use AppBundle\Event\Model\Repository\TalkInvitationRepository;
use AppBundle\Event\Model\Talk;
use AppBundle\Event\Model\TalkInvitation;
use CCMBenchmark\Ting\Driver\QueryException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class InvitationFormHandler
{
    public function __construct(
        private readonly TalkInvitationRepository $talkInvitationRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly TranslatorInterface $translator,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly Mailer $mailer,
    ) {
    }

    public function handle(Request $request, Event $event, FormInterface $form, GithubUser $user, Talk $talk): bool
    {
        $form->handleRequest($request);
        if (!$form->isSubmitted() || !$form->isValid()) {
            return false;
        }

        /** @var TalkInvitation $invitation */
        $invitation = $form->getData();
        try {
            $this->talkInvitationRepository->save($invitation);
        } catch (QueryException $exception) {
            $form->addError(new FormError($exception->getMessage()));
        }
        // Send mail to the other guy, begging for him to join the talk
        $this->eventDispatcher->addListener(KernelEvents::TERMINATE, function () use ($talk, $user, $event, $invitation): void {
            $text = $this->translator->trans('mail.invitation.text', [
                '%name%' => $user->getName() ?: $user->getLogin(),
                '%title%' => $talk->getTitle(),
                '%link%' => $this->urlGenerator->generate('cfp_invite', [
                    'eventSlug' => $event->getPath(),
                    'talkId' => $talk->getId(),
                    'token' => $invitation->getToken(),
                ], UrlGeneratorInterface::ABSOLUTE_URL),
            ]);
            $this->mailer->sendSimpleMessage('CFP Afup', $text, new MailUser($invitation->getEmail()));
        });

        return true;
    }
}
