<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members;

use AppBundle\Association\Form\UserEditType;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use AppBundle\Association\UserMembership\UserService;
use AppBundle\AuditLog\Audit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserAddAction extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserService $userService,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request): Response
    {
        $user = new User();
        $user->setRoles([]);

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
            $this->audit->log('Ajout de la personne physique ' . $user->getFirstName() . ' ' . $user->getLastName());
            $this->addFlash('notice', 'La personne physique a été ajoutée');

            return $this->redirectToRoute('admin_members_user_list', [
                'filter' => $user->getEmail(),
            ]);
        }

        return $this->render('admin/members/user_add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
