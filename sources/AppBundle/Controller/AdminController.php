<?php


namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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

    public function getMenuAction()
    {
        $pages = $this->getParameter('app.pages_backoffice');

        return $this->render(':admin:menu.html.twig', ['pages' => $pages]);
    }
}
