<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Association\Form\UserEditType;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use AppBundle\Association\UserMembership\UserService;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class UserAddAction
{
    use DbLoggerTrait;

    private UserRepository $userRepository;
    private UserService $userService;
    private FormFactoryInterface $formFactory;
    private SessionInterface $session;
    private FlashBagInterface $flashBag;
    private UrlGeneratorInterface $urlGenerator;
    private Environment $twig;

    public function __construct(
        UserRepository $userRepository,
        UserService $userService,
        FormFactoryInterface $formFactory,
        SessionInterface $session,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator,
        Environment $twig
    ) {
        $this->userRepository = $userRepository;
        $this->userService = $userService;
        $this->formFactory = $formFactory;
        $this->session = $session;
        $this->flashBag = $flashBag;
        $this->urlGenerator = $urlGenerator;
        $this->twig = $twig;
    }

    public function __invoke(Request $request)
    {
        if ($this->session->has('generer_personne_physique')) {
            $user = $this->fromSession($this->session->get('generer_personne_physique'));
            $this->session->remove('generer_personne_physique');
        } else {
            $user = new User();
            $user->setRoles([]);
        }

        $form = $this->formFactory->create(UserEditType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$user->getCompanyId()) {
                $user->setCompanyId(0);
            }
            if (null === $user->getPassword()) {
                $user->setPlainPassword($this->userService->generateRandomPassword());
            }

            $this->userRepository->create($user);
            $this->log('Ajout de la personne physique ' . $user->getFirstName() . ' ' . $user->getLastName());
            $this->flashBag->add('notice', 'La personne physique a été ajoutée');

            return new RedirectResponse($this->urlGenerator->generate('admin_members_user_list', ['filter' => $user->getEmail()]));
        }

        return new Response($this->twig->render('admin/members/user_add.html.twig', [
            'form' => $form->createView(),
        ]));
    }

    private function fromSession(array $session): User
    {
        $user = new User();

        $user->setCity($session['civilite']);
        $user->setLastname($session['nom']);
        $user->setFirstname($session['prenom']);
        $user->setEmail($session['email']);
        $user->setAddress($session['adresse']);
        $user->setZipCode($session['code_postal']);
        $user->setCity($session['ville']);
        $user->setCountry($session['id_pays']);
        $user->setPhone($session['telephone_fixe']);
        $user->setMobilephone($session['telephone_portable']);
        $user->setStatus($session['etat']);

        return $user;
    }
}
