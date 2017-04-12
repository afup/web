<?php


namespace AppBundle\Controller;


use Afup\Site\Association\Personnes_Physiques;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{
    public function loginAction()
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('admin/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
            'title' => "Connexion",
            'page' => 'connexion',
            'class' => 'panel-page'
        ));
    }

    public function lostPasswordAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('email', EmailType::class)
            ->add('submit', SubmitType::class, ['label' => 'Demander un nouveau mot de passe'])
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var $personnesPhysiques Personnes_Physiques
             */
            $personnesPhysiques = $this->get('app.legacy_model_factory')->createObject(Personnes_Physiques::class);
            $personnesPhysiques->envoyerMotDePasse($form->getData()['email']);
            $this->addFlash('notice', 'Votre demande a été prise en compte. Si un compte correspond à cet email vous recevez un nouveau mot de passe rapidement.');
        }

        return $this->render('admin/lost_password.html.twig',
            [
                'form' => $form->createView(),
                'title' => 'Mot de passe perdu',
                'page' => 'motdepasse_perdu',
                'class' => 'panel-page'
            ]
        );
    }

    public function getMenuAction()
    {
        $pages = $this->getParameter('app.pages_backoffice');

        return $this->render(':admin:menu.html.twig', ['pages' => $pages]);
    }
}
