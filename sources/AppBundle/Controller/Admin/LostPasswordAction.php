<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Association\UserMembership\UserService;
use AppBundle\Controller\BlocksHandler;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Twig\Environment;

class LostPasswordAction
{
    /** @var FormFactoryInterface */
    private $formFactory;
    /** @var BlocksHandler */
    private $blocksHandler;
    /** @var Environment */
    private $twig;
    /** @var FlashBagInterface */
    private $flashBag;
    /** @var UserService */
    private $userPasswordService;

    public function __construct(
        FormFactoryInterface $formFactory,
        UserService $userPasswordService,
        BlocksHandler $blocksHandler,
        Environment $twig,
        FlashBagInterface $flashBag
    ) {
        $this->formFactory = $formFactory;
        $this->userPasswordService = $userPasswordService;
        $this->blocksHandler = $blocksHandler;
        $this->twig = $twig;
        $this->flashBag = $flashBag;
    }

    public function __invoke(Request $request)
    {
        $form = $this->formFactory->createBuilder(FormType::class)
            ->add('email', EmailType::class)
            ->add('submit', SubmitType::class, ['label' => 'Demander un nouveau mot de passe'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userPasswordService->resetPasswordForEmail($form->getData()['email']);
            $this->flashBag->add('notice', 'Votre demande a été prise en compte. Si un compte correspond à cet email vous recevez un nouveau mot de passe rapidement.');
        }

        return new Response($this->twig->render('admin/lost_password.html.twig', [
                'form' => $form->createView(),
                'title' => 'Mot de passe perdu',
                'page' => 'motdepasse_perdu',
                'class' => 'panel-page',
            ] + $this->blocksHandler->getDefaultBlocks()));
    }
}
