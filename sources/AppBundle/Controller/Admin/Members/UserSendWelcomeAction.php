<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members;

use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\UserMembership\UserService;
use AppBundle\AuditLog\Audit;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class UserSendWelcomeAction extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserService $userService,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request): RedirectResponse
    {
        $user = $this->userRepository->get($request->query->get('id'));
        if (null === $user) {
            throw $this->createNotFoundException('Personne physique non trouvée');
        }
        try {
            $this->userService->sendWelcomeEmail($user);
            $this->audit->log('Envoi d\'un message de bienvenue à la personne physique ' . $user->getId());
            $this->addFlash('notice', 'Un mail de bienvenue a été envoyé à la personne physique');
        } catch (Exception) {
            $this->addFlash('error', 'Une erreur est survenue lors de l\'envoi du mail de bienvenue à la personne physique');
        }

        return $this->redirectToRoute('admin_members_user_list');
    }
}
