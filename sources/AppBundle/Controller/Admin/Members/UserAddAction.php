<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Association\Form\UserEditType;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use AppBundle\Association\UserMembership\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserAddAction extends AbstractController
{
    use DbLoggerTrait;

    public function __construct(
        private UserRepository $userRepository,
        private UserService $userService,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        /** @var Session $session */
        $session = $request->getSession();
        if ($session->has('generer_personne_physique')) {
            $user = $this->fromSession($session->get('generer_personne_physique'));
            $session->remove('generer_personne_physique');
        } else {
            $user = new User();
            $user->setRoles([]);
        }

        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$user->getCompanyId()) {
                $user->setCompanyId(0);
            }
            if (null === $user->getPassword()) {
                $newPassword = $this->userService->generateRandomPassword();
                $user->setPassword($this->passwordHasher->hashPassword($user, $newPassword));
            }

            $this->userRepository->create($user);
            $this->log('Ajout de la personne physique ' . $user->getFirstName() . ' ' . $user->getLastName());
            $this->addFlash('notice', 'La personne physique a été ajoutée');

            return $this->redirectToRoute('admin_members_user_list', [
                'filter' => $user->getEmail(),
            ]);
        }

        return $this->render('admin/members/user_add.html.twig', [
            'form' => $form->createView(),
        ]);
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
