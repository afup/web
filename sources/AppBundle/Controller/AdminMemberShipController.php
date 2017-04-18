<?php


namespace AppBundle\Controller;

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
use CCMBenchmark\Ting\Driver\Exception;
use Symfony\Component\HttpFoundation\Request;

class AdminMemberShipController extends SiteBaseController
{
    public function membersAction(Request $request)
    {
        /**
         * @var $companyRepository CompanyMemberRepository
         */
        $companyRepository = $this->get('ting')->get(CompanyMemberRepository::class);
        $company = $companyRepository->get($this->getUser()->getCompanyId());

        if ($company === null) {
            throw $this->createNotFoundException("Company not found");
        }

        /**
         * @var $userRepository UserRepository
         */
        $userRepository = $this->get('ting')->get(UserRepository::class);

        $users = $userRepository->loadUsersByCompany($company);

        /**
         * @var $invitationRepository CompanyMemberInvitationRepository
         */
        $invitationRepository = $this->get('ting')->get(CompanyMemberInvitationRepository::class);

        $pendingInvitations = $invitationRepository->loadPendingInvitationsByCompany($company);
        $invitationForm = $this->createForm(CompanyMemberInvitationType::class);

        if ($request->getMethod() === Request::METHOD_POST) {
            if ($request->request->has('delete_invitation')) {
                $idToDelete = $request->request->getInt('delete_invitation');
                $invitationsToDelete = array_filter(iterator_to_array($pendingInvitations), function (CompanyMemberInvitation $item) use ($idToDelete) {
                    if ($item->getId() === $idToDelete) {
                        return true;
                    }
                    return false;
                });
                if (count($invitationsToDelete) > 1) {
                    throw new \RuntimeException('Error');
                }
                /**
                 * @var $invitationToDelete CompanyMemberInvitation
                 */
                $invitationToDelete = current($invitationsToDelete);
                $invitationToDelete->setStatus(CompanyMemberInvitation::STATUS_CANCELLED);
                $invitationRepository->save($invitationToDelete);
                $this->addFlash('notice', 'L\'invitation a été supprimée.');
            }
            if ($request->request->has('resend_invitation')) {
                $idToSend = $request->request->getInt('resend_invitation');
                $invitationsToSend = array_filter(iterator_to_array($pendingInvitations), function (CompanyMemberInvitation $item) use ($idToSend) {
                    if ($item->getId() === $idToSend) {
                        return true;
                    }
                    return false;
                });
                if (count($invitationsToSend) > 1) {
                    throw new \RuntimeException('Error');
                }
                /**
                 * @var $invitationToDelete CompanyMemberInvitation
                 */
                $invitationToSend = current($invitationsToSend);
                $this->get('app.invitation_mail')->sendInvitation($company, $invitationToSend);
                $this->addFlash('notice', 'L\'invitation a bien été renvoyée');
            }
            if ($request->request->has('promote_up')) {
                $idToPromote = $request->request->getInt('promote_up');
                $usersToPromote = array_filter(iterator_to_array($users), function (User $item) use ($idToPromote) {
                    if ($item->getId() === $idToPromote) {
                        return true;
                    }
                    return false;
                });
                if (count($usersToPromote) > 1) {
                    throw new \RuntimeException('Error');
                }
                /**
                 * @var $userToPromote User
                 */
                $userToPromote = current($usersToPromote);
                $userToPromote->addRole('ROLE_COMPANY_MANAGER');
                $userRepository->save($userToPromote);
                $this->addFlash('notice', 'Le membre a été promu en tant que manager.');
            }
            if ($request->request->has('promote_down')) {
                $idToPromote = $request->request->getInt('promote_down');
                $usersToPromote = array_filter(iterator_to_array($users), function (User $item) use ($idToPromote) {
                    if ($item->getId() === $idToPromote) {
                        return true;
                    }
                    return false;
                });
                if (count($usersToPromote) > 1) {
                    throw new \RuntimeException('Error');
                }
                /**
                 * @var $userToPromote User
                 */
                $userToPromote = current($usersToPromote);
                $userToPromote->removeRole('ROLE_COMPANY_MANAGER');
                $userRepository->save($userToPromote);
                $this->addFlash('notice', 'Le membre n\'a plus accès la gestion de l\'entreprise.');
            }

            return $this->redirectToRoute('admin_company_members');
        }

        // Afficher la liste des invitations en attente & renvoyer si nécessaire

        // Pouvoir gérer les droits manager ou non

        // Pouvoir supprimer un membre == suppression du rattachement à l'entreprise, le compte continue d'exister sans cotisation
        // Penser dans ce cas à faire les désabonnements de rigueur: newsletter, etc.
        // Enlever l'éventuel role ROLE_COMPANY_MANAGER

        // Pouvoir envoyer une nouvelle invitation

        // Pouvoir upgrader son compte

        return $this->render(':admin/association/membership:members_company.html.twig',
            [
                'title' => 'Les membres de mon entreprise',
                'users' => $users,
                'invitations' => $pendingInvitations,
                'formInvitation' => $invitationForm->createView()
            ]
        );
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
