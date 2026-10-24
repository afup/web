<?php

declare(strict_types=1);

namespace AppBundle\Controller\Auth;

use AppBundle\Association\PasswordReset\PasswordResetRateLimiter;
use AppBundle\Association\PasswordReset\PasswordResetService;
use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LostPasswordAction extends AbstractController
{
    public function __construct(
        private readonly PasswordResetService $passwordResetService,
        private readonly PasswordResetRateLimiter $passwordResetRateLimiter,
        private readonly ViewRenderer $view,
    ) {}

    public function __invoke(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('email', EmailType::class)
            ->add('submit', SubmitType::class, ['label' => 'Recevoir un lien de réinitialisation'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var array{email: string} $data */
            $data = $form->getData();
            $email = $data['email'];

            if ($this->passwordResetRateLimiter->allows($request->getClientIp(), $email)) {
                $this->passwordResetService->requestResetForEmail($email);
            }

            // Même message quel que soit le cas — email inconnu ou quota atteint :
            // rien ne doit permettre de deviner qu'un compte existe.
            $this->addFlash('notice', sprintf(
                'Votre demande a été prise en compte. Si un compte correspond à cet email, vous recevrez un lien pour choisir un nouveau mot de passe. Ce lien est valable %d minutes.',
                $this->passwordResetService->getValidityInMinutes(),
            ));
        }

        return $this->view->render('site/auth/lost_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
