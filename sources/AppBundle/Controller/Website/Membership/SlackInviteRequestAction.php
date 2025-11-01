<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Membership;

use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use AppBundle\AuditLog\Audit;
use AppBundle\Slack\LegacyClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class SlackInviteRequestAction extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly LegacyClient $legacyClient,
        private readonly Audit $audit,
    ) {}

    public function __invoke(): RedirectResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException("Vous n'êtes pas connecté");
        }
        if (!$user->canRequestSlackInvite()) {
            throw $this->createAccessDeniedException("Vous n'êtes pas autorité à demander une invitation");
        }
        $this->legacyClient->invite($user->getEmail());
        $this->addFlash('success', 'Un email vous a été envoyé pour rejoindre le Slack des membres !');
        $user->setSlackInviteStatus(User::SLACK_INVITE_STATUS_REQUESTED);
        $this->userRepository->save($user);
        $this->audit->log('Demande invitation slack');
        return $this->redirectToRoute('admin_home');
    }
}
