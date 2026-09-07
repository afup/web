<?php

declare(strict_types=1);

namespace AppBundle\Controller\Auth;

use AppBundle\Association\Form\ResetPasswordType;
use AppBundle\Association\PasswordReset\PasswordResetService;
use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ResetPasswordAction extends AbstractController
{
    public function __construct(
        private readonly PasswordResetService $passwordResetService,
        private readonly ViewRenderer $view,
    ) {}

    public function __invoke(Request $request, string $token): Response
    {
        $passwordResetToken = $this->passwordResetService->findValidToken($token);
        if ($passwordResetToken === null) {
            // Jeton expiré, déjà utilisé ou inexistant : un seul et même message,
            // pour ne rien apprendre à qui tenterait sa chance.
            return $this->withoutReferrer($this->view->render('site/auth/reset_password_invalid.html.twig'));
        }

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            if ($this->passwordResetService->reset($passwordResetToken, $plainPassword)) {
                $this->addFlash('success', 'Votre mot de passe a été modifié. Vous pouvez maintenant vous connecter.');

                return $this->withoutReferrer($this->redirectToRoute('app_login'));
            }

            return $this->withoutReferrer($this->view->render('site/auth/reset_password_invalid.html.twig'));
        }

        return $this->withoutReferrer($this->view->render('site/auth/reset_password.html.twig', [
            'form' => $form->createView(),
        ]));
    }

    /**
     * Le jeton est dans l'URL : aucun lien de cette page ne doit le propager.
     */
    private function withoutReferrer(Response $response): Response
    {
        $response->headers->set('Referrer-Policy', 'no-referrer');

        return $response;
    }
}
