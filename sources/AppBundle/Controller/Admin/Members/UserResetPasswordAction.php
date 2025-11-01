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

class UserResetPasswordAction extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserService $userPasswordService,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request): RedirectResponse
    {
        $user = $this->userRepository->get($request->query->get('id'));
        if (null === $user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }
        try {
            $this->userPasswordService->resetPassword($user);
            $this->audit->log('Envoi d\'un nouveau mot de passe à la personne physique ' . $user->getId());
            $this->addFlash('notice', 'Un nouveau mot de passe a été envoyé à la personne physique');
        } catch (Exception) {
            $this->addFlash('error', 'Une erreur est survenue lors de l\'envoi d\'un nouveau mot de passe à la personne physique');
        }

        return $this->redirectToRoute('admin_members_user_list');
    }
}
