<?php

namespace AppBundle\Controller\Admin\Members;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Association\Form\UserEditFormData;
use AppBundle\Association\Form\UserEditFormDataFactory;
use AppBundle\Association\Form\UserEditType;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use AppBundle\Association\UserMembership\UserService;
use Exception;
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

    /** @var UserRepository */
    private $userRepository;
    /** @var UserEditFormDataFactory */
    private $userEditFormDataFactory;
    /** @var UserService */
    private $userService;
    /** @var FormFactoryInterface */
    private $formFactory;
    /** @var SessionInterface */
    private $session;
    /** @var FlashBagInterface */
    private $flashBag;
    /** @var UrlGeneratorInterface */
    private $urlGenerator;
    /** @var Environment */
    private $twig;

    public function __construct(
        UserRepository $userRepository,
        UserEditFormDataFactory $userEditFormDataFactory,
        UserService $userService,
        FormFactoryInterface $formFactory,
        SessionInterface $session,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator,
        Environment $twig
    ) {
        $this->userRepository = $userRepository;
        $this->userEditFormDataFactory = $userEditFormDataFactory;
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
            $data = $this->userEditFormDataFactory->fromSession($this->session->get('generer_personne_physique'));
            $this->session->remove('generer_personne_physique');
        } else {
            $data = new UserEditFormData();
            $data->roles = json_encode([]);
        }
        $form = $this->formFactory->create(UserEditType::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = new User();
            if (null === $data->password) {
                $data->password = $this->userService->generateRandomPassword();
            }
            $this->userEditFormDataFactory->toUser($data, $user);
            try {
                $this->userRepository->create($user);
                $this->log('Ajout de la personne physique ' . $user->getFirstName() . ' ' . $user->getLastName());
                $this->flashBag->add('notice', 'La personne physique a été ajoutée');

                return new RedirectResponse($this->urlGenerator->generate('admin_members_user_list', ['filter' => $user->getEmail()]));
            } catch (Exception $e) {
                $this->flashBag->add('error', 'Une erreur est survenue lors de l\'ajout de la personne physique');
            }
        }

        return new Response($this->twig->render('admin/members/user_add.html.twig', [
            'form' => $form->createView(),
        ]));
    }
}
