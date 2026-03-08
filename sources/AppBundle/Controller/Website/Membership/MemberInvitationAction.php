<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Membership;

use AppBundle\Association\Event\NewMemberEvent;
use AppBundle\Association\Form\UserType;
use AppBundle\Association\Model\CompanyMemberInvitation;
use AppBundle\Association\Model\Repository\CompanyMemberInvitationRepository;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class MemberInvitationAction extends AbstractController
{
    public function __construct(
        private readonly ViewRenderer $view,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly UserRepository $userRepository,
        private readonly CompanyMemberRepository $companyMemberRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly CompanyMemberInvitationRepository $companyMemberInvitationRepository,
    ) {}

    public function __invoke(Request $request, int $invitationId, string $token): Response
    {
        $invitation = $this->companyMemberInvitationRepository->getOneBy(['id' => $invitationId, 'token' => $token, 'status' => CompanyMemberInvitation::STATUS_PENDING]);
        $company = null;
        if ($invitation) {
            $company = $this->companyMemberRepository->get($invitation->getCompanyId());
        }

        if ($invitation === null || $company === null) {
            throw $this->createNotFoundException(sprintf('Could not find invitation with token "%s"', $token));
        }

        $userForm = $this->createForm(UserType::class);

        $userForm->handleRequest($request);
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            /**
             * @var User $user
             */
            $user = $userForm->getData();
            $user->setCivility('');
            $hash = $this->passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hash);
            $user
                ->setStatus(User::STATUS_ACTIVE)
                ->setCompanyId($company->getId())
            ;

            if ($invitation->getManager()) {
                $user->setRoles(['ROLE_COMPANY_MANAGER', 'ROLE_USER']);
            }

            $invitation->setStatus(CompanyMemberInvitation::STATUS_ACCEPTED);

            $this->userRepository->save($user);
            $this->companyMemberInvitationRepository->save($invitation);
            $this->addFlash('success', 'Votre compte a été créé !');

            $this->eventDispatcher->dispatch(new NewMemberEvent($user));

            return $this->redirectToRoute('member_index');
        }

        return $this->view->render('site/company_membership/member_invitation.html.twig', [
            'company' => $company,
            'form' => $userForm->createView(),
        ]);
    }
}
