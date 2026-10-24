<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members;

use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\PasswordReset\PasswordResetService;
use AppBundle\AuditLog\Audit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class UserResetPasswordAction extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly PasswordResetService $passwordResetService,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request): RedirectResponse
    {
        $user = $this->userRepository->get($request->query->get('id'));
        if (null === $user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        // Mailer::send() avale les exceptions et renvoie false : c'est la valeur de
        // retour, et non une exception, qui signale l'échec d'envoi.
        if ($this->passwordResetService->createTokenFor($user)) {
            $this->audit->log('Envoi d\'un lien de réinitialisation de mot de passe à la personne physique ' . $user->getId());
            $this->addFlash('notice', 'Un lien de réinitialisation de mot de passe a été envoyé à la personne physique');
        } else {
            $this->addFlash('error', 'Une erreur est survenue lors de l\'envoi du lien de réinitialisation de mot de passe à la personne physique');
        }

        return $this->redirectToRoute('admin_members_user_list');
    }
}
