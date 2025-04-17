<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Association\Form\UserBadgeType;
use AppBundle\Association\Form\UserEditType;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Event\Model\Repository\UserBadgeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserEditAction extends AbstractController
{
    use DbLoggerTrait;

    private UserRepository $userRepository;
    private UserBadgeRepository $userBadgeRepository;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        UserRepository $userRepository,
        UserBadgeRepository $userBadgeRepository,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->userRepository = $userRepository;
        $this->userBadgeRepository = $userBadgeRepository;
        $this->passwordHasher = $passwordHasher;
    }

    public function __invoke(Request $request): Response
    {
        $user = $this->userRepository->get($request->query->get('id'));
        if (!$user) {
            $this->addFlash('error', 'Utilisateur non trouvé');
            return $this->redirectToRoute('admin_members_user_list');
        }
        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Save password if not empty
            $newPassword = $form->get('plainPassword')->getViewData()['first'];
            if ($newPassword) {
                $user->setPassword($this->passwordHasher->hashPassword($user, $newPassword));
            }

            $this->userRepository->edit($user);
            $this->log('Modification de la personne physique ' . $user->getFirstName() . ' ' . $user->getLastName() . ' (' . $user->getId() . ')');
            // Redirection sur la liste filtrée
            $this->addFlash('notice', 'La personne physique a été modifiée');

            return $this->redirectToRoute('admin_members_user_list', [
                'filter' => $user->getEmail()
            ]);
        }

        $userBadges = iterator_to_array($this->userBadgeRepository->findByUserId($user->getId()));
        $userBadgeForm = $this->createForm(UserBadgeType::class, [], [
            'user' => $user,
            'action' => $this->generateUrl('admin_members_user_badge_new', [
                'user_id' => $user->getId()
            ]),
        ]);

        return $this->render('admin/members/user_edit.html.twig', [
            'user' => $user,
            'user_badges' => $userBadges,
            'user_badge_form' => $userBadgeForm->createView(),
            'form' => $form->createView(),
        ]);
    }
}
