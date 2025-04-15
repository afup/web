<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin;

use AppBundle\Association\UserMembership\UserService;
use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LostPasswordAction extends AbstractController
{
    private ViewRenderer $view;
    private UserService $userPasswordService;

    public function __construct(UserService $userPasswordService,
                                ViewRenderer $view
    ) {
        $this->userPasswordService = $userPasswordService;
        $this->view = $view;
    }

    public function __invoke(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('email', EmailType::class)
            ->add('submit', SubmitType::class, ['label' => 'Demander un nouveau mot de passe'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userPasswordService->resetPasswordForEmail($form->getData()['email']);
            $this->addFlash('notice', 'Votre demande a été prise en compte. Si un compte correspond à cet email vous recevez un nouveau mot de passe rapidement.');
        }

        return $this->view->render('admin/lost_password.html.twig', [
            'form' => $form->createView(),
            'title' => 'Mot de passe perdu',
            'page' => 'motdepasse_perdu',
            'class' => 'panel-page',
        ]);
    }
}
