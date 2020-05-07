<?php

namespace AppBundle\Controller\Admin;

use Afup\Site\Association\Personnes_Physiques;
use AppBundle\Controller\BlocksHandler;
use AppBundle\LegacyModelFactory;
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
    /** @var LegacyModelFactory */
    private $legacyModelFactory;
    /** @var BlocksHandler */
    private $blocksHandler;
    /** @var Environment */
    private $twig;
    /** @var FlashBagInterface */
    private $flashBag;

    public function __construct(
        FormFactoryInterface $formFactory,
        LegacyModelFactory $legacyModelFactory,
        BlocksHandler $blocksHandler,
        Environment $twig,
        FlashBagInterface $flashBag
    ) {
        $this->formFactory = $formFactory;
        $this->legacyModelFactory = $legacyModelFactory;
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
            /** @var $personnesPhysiques Personnes_Physiques */
            $personnesPhysiques = $this->legacyModelFactory->createObject(Personnes_Physiques::class);
            $personnesPhysiques->envoyerMotDePasse($form->getData()['email']);
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
