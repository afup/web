<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Membership;

use Afup\Site\Utils\Logs;
use AppBundle\Association\Factory\UserFactory;
use AppBundle\Association\Form\RegisterUserType;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\UserMembership\UserService;
use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;

final class RegisterAction extends AbstractController
{
    public function __construct(
        private readonly ViewRenderer $view,
        private readonly UserFactory $userFactory,
        private readonly UserRepository $userRepository,
        private readonly UserService $userService,
        private readonly UserAuthenticatorInterface $userAuthenticator,
        #[Autowire('@security.authenticator.form_login.legacy_secured_area')]
        private readonly FormLoginAuthenticator $formLoginAuthenticator,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {}

    public function __invoke(Request $request): Response
    {
        $user = $this->userFactory->createForRegister();

        $form = $this->createForm(RegisterUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $this->passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hash);
            $this->userRepository->save($user);

            Logs::initialiser($GLOBALS['AFUP_DB'], $user->getId());
            Logs::log('Ajout de la personne physique ' . $user->getFirstName() . ' ' . $user->getLastName());

            $this->userService->sendWelcomeEmail($user);
            $this->addFlash('notice', 'Merci pour votre inscription. Il ne reste plus qu\'à régler votre cotisation.');

            return $this->userAuthenticator->authenticateUser($user, $this->formLoginAuthenticator, $request);
        }

        return $this->view->render('admin/association/membership/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
