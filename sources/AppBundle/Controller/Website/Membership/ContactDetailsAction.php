<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Membership;

use AppBundle\Association\Form\ContactDetailsType;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\AuditLog\Audit;
use AppBundle\Security\Authentication;
use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class ContactDetailsAction extends AbstractController
{
    public function __construct(
        private readonly ViewRenderer $view,
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly Authentication $authentication,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request): Response
    {
        $user = $this->authentication->getAfupUser();

        $userForm = $this->createForm(ContactDetailsType::class, $user);
        $userForm->handleRequest($request);
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            // Save password if not empty
            $newPassword = $userForm->get('plainPassword')->getViewData()['first'];
            if ($newPassword) {
                $hash = $this->passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hash);
            }

            $this->userRepository->save($user);

            $this->audit->log("Modification des coordonnées de l'utilisateur " . $user->getUsername() . " effectuée avec succès.");
            $this->addFlash('success', 'Votre compte a été modifié !');
        }

        return $this->view->render('admin/association/membership/member_contact_details.html.twig', [
            'title' => 'Mes coordonnées',
            'form' => $userForm->createView(),
        ]);
    }
}
