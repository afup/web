<?php


namespace AppBundle\Controller;

use Afup\Site\Association\Personnes_Physiques;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends SiteBaseController
{
    public function loginAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $actualUrl = $request->getSchemeAndHttpHost() . $request->getRequestUri();
        $targetPath = null;
        if (
            $request->query->has('target')
            and $targetUri = $request->query->get('target')
            and $targetUri !== $actualUrl
            and parse_url($targetUri, PHP_URL_HOST) === null // Ensure there is no domain here
        ) {
            $targetPath = $targetUri;
        }

        return $this->render('admin/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
            'target_path'   => $targetPath,
            'title' => "Connexion",
            'page' => 'connexion',
            'class' => 'panel-page'
        ]);
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
            $personnesPhysiques = $this->get(\AppBundle\LegacyModelFactory::class)->createObject(Personnes_Physiques::class);
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

        $masterRequest = $this->get('request_stack')->getMasterRequest();

        $page = $masterRequest->query->get('page');
        $route = $masterRequest->get('_route');

        $currentGroupKey = null;
        $currentElementKey = null;

        foreach ($pages as $groupKey => $group) {
            if (isset($group['elements'])) {
                foreach ($group['elements'] as $elementKey => $element) {
                    if ($elementKey == $page
                        || (isset($element['extra_routes']) && in_array($route, $element['extra_routes']))
                    ) {
                        $currentGroupKey = $groupKey;
                        $currentElementKey = $elementKey;
                    }
                }
            }
        }

        return $this->render(
            ':admin:menu.html.twig',
            [
                'pages' => $pages,
                'current_group_key' => $currentGroupKey,
                'current_element_key' => $currentElementKey,
            ]
        );
    }
}
