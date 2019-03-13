<?php


namespace AppBundle\Controller;

use AppBundle\Association\CompanyMembership\UserCompany;
use AppBundle\Association\Form\AdminCompanyMemberType;
use AppBundle\Association\Form\CompanyMemberInvitationType;
use AppBundle\Association\Form\UserType;
use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\CompanyMemberInvitation;
use AppBundle\Association\Model\Repository\CompanyMemberInvitationRepository;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Association\Model\Repository\SubscriptionReminderLogRepository;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use AppBundle\Model\CollectionFilter;
use CCMBenchmark\Ting\Driver\Exception;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;

class AdminMemberShipController extends SiteBaseController
{
    public function membersAction(Request $request, $id = null)
    {
        /**
         * @var $companyRepository CompanyMemberRepository
         */
        $companyRepository = $this->get('ting')->get(CompanyMemberRepository::class);
        /**
         * @var $company CompanyMember
         */
        $companyId = ($id && $this->isGranted('ROLE_SUPER_ADMIN')) ? $id : $this->getUser()->getCompanyId();
        $company = $companyRepository->get($companyId);

        if ($company === null) {
            throw $this->createNotFoundException("Company not found");
        }

        /**
         * @var $userRepository UserRepository
         */
        $userRepository = $this->get('ting')->get(UserRepository::class);

        $users = $userRepository->loadActiveUsersByCompany($company);

        /**
         * @var $invitationRepository CompanyMemberInvitationRepository
         */
        $invitationRepository = $this->get('ting')->get(CompanyMemberInvitationRepository::class);

        $pendingInvitations = $invitationRepository->loadPendingInvitationsByCompany($company);

        $invitation = new CompanyMemberInvitation();
        $invitationForm = $this->createForm(CompanyMemberInvitationType::class, $invitation);
        $invitationForm->handleRequest($request);

        $filter = $this->get(\AppBundle\Model\CollectionFilter::class);

        $canAddUser = false;
        if (($pendingInvitations->count() + $users->count()) < $company->getMaxMembers()) {
            $canAddUser = true;
        }

        if ($request->getMethod() === Request::METHOD_POST) {
            $userCompany = $this->get(\AppBundle\Association\CompanyMembership\UserCompany::class);

            if ($invitationForm->isSubmitted() && $invitationForm->isValid()) {
                if ($canAddUser === false) {
                    $this->addFlash('error', 'Vous avez atteint le nombre maximum de membres');
                } else {
                    return $this->forward('AppBundle:AdminMemberShip:addUser',
                        [
                            'company' => $company,
                            'invitation' => $invitation,
                            'users' => $users,
                            'pendingInvitations' => $pendingInvitations,
                            'invitationRepository' => $invitationRepository,
                            'filter' => $filter
                        ]
                    );
                }
            } elseif ($this->isCsrfTokenValid('admin_company_members', $request->request->get('token')) === false) {
                $this->addFlash('error', 'Erreur lors de la soumission du formulaire (jeton CSRF invalide). Merci de réessayer.');
                return $this->redirectToRoute('admin_company_members');
            } elseif ($request->request->has('delete_invitation')) {
                $emailToDelete = $request->request->get('delete_invitation');

                return $this->forward('AppBundle:AdminMemberShip:removeInvitation',
                    [
                        'emailToDelete' => $emailToDelete,
                        'filter' => $filter,
                        'pendingInvitations' => $pendingInvitations,
                        'invitationRepository' => $invitationRepository
                    ]
                );
            } elseif ($request->request->has('resend_invitation')) {
                $emailToSend = $request->request->get('resend_invitation');
                return $this->forward('AppBundle:AdminMemberShip:resendInvitation',
                    [
                        'emailToSend' => $emailToSend,
                        'filter' => $filter,
                        'pendingInvitations' => $pendingInvitations,
                        'company' => $company
                    ]
                );
            } elseif ($request->request->has('promote_up')) {
                $emailToPromote = $request->request->get('promote_up');
                return $this->forward('AppBundle:AdminMemberShip:promoteUser',
                    [
                        'emailToPromote' => $emailToPromote,
                        'filter' => $filter,
                        'users' => $users,
                        'userCompany' => $userCompany
                    ]
                );
            } elseif ($request->request->has('promote_down')) {
                $emailToDisapprove = $request->request->get('promote_down');
                return $this->forward('AppBundle:AdminMemberShip:disapproveUser',
                    [
                        'emailToDisapprove' => $emailToDisapprove,
                        'filter' => $filter,
                        'users' => $users,
                        'userCompany' => $userCompany
                    ]
                );
            } elseif ($request->request->has('remove')) {
                $emailToRemove = $request->request->get('remove');

                return $this->forward('AppBundle:AdminMemberShip:removeUser',
                    [
                        'emailToRemove' => $emailToRemove,
                        'filter' => $filter,
                        'users' => $users,
                        'userCompany' => $userCompany
                    ]
                );
            }

            return $this->redirectToRoute('admin_company_members');
        }

        return $this->render(':admin/association/membership:members_company.html.twig',
            [
                'title' => 'Les membres de mon entreprise',
                'users' => $users,
                'invitations' => $pendingInvitations,
                'formInvitation' => $invitationForm->createView(),
                'company' => $company,
                'canAddUser' => $canAddUser,
                'token' => $this->get('security.csrf.token_manager')->getToken('admin_company_members')
            ]
        );
    }

    public function addUserAction(
        CompanyMember $company,
        CompanyMemberInvitation $invitation,
        CollectionInterface $users,
        CollectionInterface $pendingInvitations,
        CompanyMemberInvitationRepository $invitationRepository,
        CollectionFilter $filter
    ) {
        // Check if there is already a pending invitation for this email and this company
        $matchingUser = $filter->findOne($users, 'getEmail', $invitation->getEmail());
        $matchingInvitation = $filter->findOne($pendingInvitations, 'getEmail', $invitation->getEmail());

        if ($matchingInvitation !== null || $matchingUser !== null) {
            $this->addFlash('error', 'Vous ne pouvez pas envoyer plusieurs invitations au même email.');
            return $this->redirectToRoute('admin_company_members');
        }

        // Handle invitation
        $invitation
            ->setSubmittedOn(new \DateTime())
            ->setCompanyId($company->getId())
            ->setToken(base64_encode(random_bytes(30)))
            ->setStatus(CompanyMemberInvitation::STATUS_PENDING)
        ;
        $invitationRepository->save($invitation);

        // Send mail to the other guy, begging for him to join the company
        $this->get('event_dispatcher')->addListener(KernelEvents::TERMINATE, function () use ($company, $invitation) {
            $this->get(\AppBundle\Association\CompanyMembership\InvitationMail::class)->sendInvitation($company, $invitation);
        });
        $this->addFlash(
            'notice',
            sprintf('L\'invitation a été envoyée à l\'adresse %s.', $invitation->getEmail())
        );
        return $this->redirectToRoute('admin_company_members', [
            'id' => $this->isGranted('ROLE_SUPER_ADMIN') ? $company->getId() : null,
        ]);
    }

    public function removeUserAction(
        $emailToRemove,
        CollectionFilter $filter,
        CollectionInterface $users,
        UserCompany $userCompany)
    {
        /**
         * @var $user User
         */
        $user = $filter->findOne($users, 'getEmail', $emailToRemove);

        if ($user === null) {
            $this->addFlash('error', 'Une erreur est survenue lors de la suppression de ce compte');
        } elseif ($user->getId() === $this->getUser()->getId()) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer votre propre compte');
        } else {
            $userCompany->disableUser($user);
            $this->addFlash('notice', 'Le compte a été supprimer de votre adhésion entreprise.');
        }

        return $this->redirectToRoute('admin_company_members', [
            'id' => $this->isGranted('ROLE_SUPER_ADMIN') ? $user->getCompanyId() : null,
        ]);
    }

    public function promoteUserAction(
        $emailToPromote,
        CollectionFilter $filter,
        CollectionInterface $users,
        UserCompany $userCompany
    ) {
        /**
         * @var $user User
         */
        $user = $filter->findOne($users, 'getEmail', $emailToPromote);
        if ($user !== null) {
            $userCompany->setManager($user);
            $this->addFlash('notice', 'Le membre a été promu en tant que manager.');
        } else {
            $this->addFlash('error', 'Une erreur est survenue lors de l\'ajout des droits de gestion à ce membre');
        }

        return $this->redirectToRoute('admin_company_members', [
            'id' => $this->isGranted('ROLE_SUPER_ADMIN') ? $user->getCompanyId() : null,
        ]);
    }

    public function disapproveUserAction(
        $emailToDisapprove,
        CollectionFilter $filter,
        CollectionInterface $users,
        UserCompany $userCompany
    ) {
        /**
         * @var $user User
         */
        $user = $filter->findOne($users, 'getEmail', $emailToDisapprove);

        if ($user === null) {
            $this->addFlash('error', 'Une erreur est survenue lors de la suppression des droits de gestion de ce membre');
        } elseif ($user->getId() === $this->getUser()->getId()) {
            $this->addFlash('error', 'Vous ne pouvez pas enlever vos droits de gestion');
        } else {
            $userCompany->unsetManager($user);
            $this->addFlash('notice', 'Le membre n\'a plus accès la gestion de l\'entreprise.');
        }

        return $this->redirectToRoute('admin_company_members', [
            'id' => $this->isGranted('ROLE_SUPER_ADMIN') ? $user->getCompanyId() : null,
        ]);
    }

    public function resendInvitationAction(
        $emailToSend,
        CollectionFilter $filter,
        CollectionInterface $pendingInvitations,
        CompanyMember $company
    ) {
        /**
         * @var $invitationToSend CompanyMemberInvitation
         */
        $invitationToSend = $filter->findOne($pendingInvitations, 'getEmail', $emailToSend);
        if ($invitationToSend !== null) {
            $this->get(\AppBundle\Association\CompanyMembership\InvitationMail::class)->sendInvitation($company, $invitationToSend);
            $this->addFlash('notice', 'L\'invitation a été renvoyée');
        } else {
            $this->addFlash('error', 'Une erreur est survenue lors de l\'envoi de l\'invitation');
        }

        return $this->redirectToRoute('admin_company_members', [
            'id' => $this->isGranted('ROLE_SUPER_ADMIN') ? $company->getId() : null,
        ]);
    }

    public function removeInvitationAction(
        $emailToDelete,
        CollectionFilter $filter,
        CollectionInterface $pendingInvitations,
        CompanyMemberInvitationRepository $invitationRepository
    ) {
        /**
         * @var $invitationToDelete CompanyMemberInvitation
         */
        $invitationToDelete = $filter->findOne($pendingInvitations, 'getEmail', $emailToDelete);
        if ($invitationToDelete !== null) {
            $invitationToDelete->setStatus(CompanyMemberInvitation::STATUS_CANCELLED);
            $invitationRepository->save($invitationToDelete);
            $this->addFlash('notice', 'L\'invitation a été annulée.');
        } else {
            $this->addFlash('error', 'Une erreur a été rencontrée lors de la suppression de cette invitation');
        }
        return $this->redirectToRoute('admin_company_members');
    }

    public function companyAction(Request $request)
    {
        /**
         * @var $companyRepository CompanyMemberRepository
         */
        $companyRepository = $this->get('ting')->get(CompanyMemberRepository::class);
        $company = $companyRepository->get($this->getUser()->getCompanyId());

        if ($company === null) {
            throw $this->createNotFoundException("Company not found");
        }

        $subscribeForm = $this->createForm(AdminCompanyMemberType::class, $company);
        $subscribeForm->handleRequest($request);

        if ($subscribeForm->isSubmitted() && $subscribeForm->isValid()) {
            /**
             * @var $member CompanyMember
             */
            $member = $subscribeForm->getData();
            try {
                $companyRepository->save($member);
                $this->addFlash('notice', 'Les modifications ont bien été enregistrées.');
            } catch (Exception $exception) {
                $this->addFlash('error', 'Une erreur est survenue. Merci de nous contacter.');
            }
            return $this->redirectToRoute('admin_company');
        }

        return $this->render(
            ':admin/association/membership:company.html.twig',
            [
                'title' => 'Mon adhésion entreprise',
                'form' => $subscribeForm->createView()
            ]
        );
    }

    public function memberInvitationAction(Request $request, $invitationId, $token)
    {
        /**
         * @var $invitationRepository CompanyMemberInvitationRepository
         */
        $invitationRepository = $this->get('ting')->get(CompanyMemberInvitationRepository::class);

        /**
         * @var $invitation CompanyMemberInvitation
         */
        $invitation = $invitationRepository->getOneBy(['id' => $invitationId, 'token' => $token, 'status' => CompanyMemberInvitation::STATUS_PENDING]);
        $company = null;
        if ($invitation) {
            /**
             * @var $company CompanyMember
             */
            $company = $this->get('ting')->get(CompanyMemberRepository::class)->get($invitation->getCompanyId());
        }

        if ($invitation === null || $company === null) {
            throw $this->createNotFoundException(sprintf('Could not find invitation with token "%s"', $token));
        }

        $userForm = $this->createForm(UserType::class);

        $userForm->handleRequest($request);
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            /**
             * @var $user User
             */
            $user = $userForm->getData();
            $user
                ->setStatus(User::STATUS_ACTIVE)
                ->setCompanyId($company->getId())
                ->setPassword(md5($user->getPassword()))/** @TODO We should change that */
            ;

            if ($invitation->getManager()) {
                $user->setRoles(['ROLE_COMPANY_MANAGER', 'ROLE_USER']);
            }

            $invitation->setStatus(CompanyMemberInvitation::STATUS_ACCEPTED);

            $this->get('ting')->get(UserRepository::class)->save($user);
            $invitationRepository->save($invitation);
            $this->addFlash('success', 'Votre compte a été créé !');

            return $this->redirect('/pages/administration/');
        }

        return $this->render(':site/company_membership:member_invitation.html.twig', ['company' => $company, 'form' => $userForm->createView()]);
    }

    public function reminderLogAction($page = 1)
    {
        /**
         * @var $repository SubscriptionReminderLogRepository
         */
        $limit = 50;
        $repository = $this->get('ting')->get(SubscriptionReminderLogRepository::class);
        $results = $repository->getPaginatedLogs($page, $limit);

        return $this->render(':admin/relances:liste.html.twig', [
            'logs' => $results,
            'limit' => $limit,
            'page' => $page,
            'title' => 'Relances'
        ]);
    }
}
