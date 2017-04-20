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
use CCMBenchmark\Ting\Repository\CollectionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;

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

        $users = $userRepository->loadActiveUsersByCompany($company);

        /**
         * @var $invitationRepository CompanyMemberInvitationRepository
         */
        $invitationRepository = $this->get('ting')->get(CompanyMemberInvitationRepository::class);

        $pendingInvitations = $invitationRepository->loadPendingInvitationsByCompany($company);

        $invitation = new CompanyMemberInvitation();
        $invitationForm = $this->createForm(CompanyMemberInvitationType::class, $invitation);
        $invitationForm->handleRequest($request);

        if ($request->getMethod() === Request::METHOD_POST) {
            $userCompany = $this->get('app.user_company');

            if ($invitationForm->isSubmitted() && $invitationForm->isValid()) {
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
                    $this->get('app.invitation_mail')->sendInvitation($company, $invitation);
                });
                $this->addFlash(
                    'notice',
                    sprintf('L\'invitation a été envoyée à l\'adresse %s.', $invitation->getEmail())
                );
            } elseif ($this->isCsrfTokenValid('admin_company_members', $request->request->get('token')) === false) {
                $this->addFlash('error', 'Erreur lors de la soumission du formulaire (jeton CSRF invalide). Merci de réessayer.');
                return $this->redirectToRoute('admin_company_members');
            } elseif ($request->request->has('delete_invitation')) {
                $idToDelete = $request->request->getInt('delete_invitation');

                /**
                 * @var $invitationToDelete CompanyMemberInvitation
                 */
                $invitationToDelete = $this->getItemFromListById($idToDelete, $pendingInvitations);
                $invitationToDelete->setStatus(CompanyMemberInvitation::STATUS_CANCELLED);
                $invitationRepository->save($invitationToDelete);
                $this->addFlash('notice', 'L\'invitation a été annulée.');
            } elseif ($request->request->has('resend_invitation')) {
                $idToSend = $request->request->getInt('resend_invitation');

                /**
                 * @var $invitationToSend CompanyMemberInvitation
                 */
                $invitationToSend = $this->getItemFromListById($idToSend, $pendingInvitations);
                $this->get('app.invitation_mail')->sendInvitation($company, $invitationToSend);
                $this->addFlash('notice', 'L\'invitation a été renvoyée');
            } elseif ($request->request->has('promote_up')) {
                $idToPromote = $request->request->getInt('promote_up');

                /**
                 * @var $user User
                 */
                $user = $this->getItemFromListById($idToPromote, $users);

                $userCompany->setManager($user);
                $this->addFlash('notice', 'Le membre a été promu en tant que manager.');
            } elseif ($request->request->has('promote_down')) {
                $idToPromote = $request->request->getInt('promote_down');

                /**
                 * @var $user User
                 */
                $user = $this->getItemFromListById($idToPromote, $users);

                if ($user->getId() === $this->getUser()->getId()) {
                    $this->addFlash('error', 'Vous ne pouvez pas enlever vos droits de gestion');
                } else {
                    $userCompany->unsetManager($user);
                    $this->addFlash('notice', 'Le membre n\'a plus accès la gestion de l\'entreprise.');
                }
            } elseif ($request->request->has('remove')) {
                $idToRemove = $request->request->getInt('remove');
                /**
                 * @var $user User
                 */
                $user = $this->getItemFromListById($idToRemove, $users);
                if ($user->getId() === $this->getUser()->getId()) {
                    $this->addFlash('error', 'Vous ne pouvez pas détacher votre propre compte');
                } else {
                    $userCompany->disableUser($user);
                    $this->addFlash('notice', 'Le compte a été détaché de votre adhésion entreprise.');
                }
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
                'token' => $this->get('security.csrf.token_manager')->getToken('admin_company_members')
            ]
        );
    }

    /**
     * @param $id
     * @param CollectionInterface $list
     * @return Object
     */
    private function getItemFromListById($id, CollectionInterface $list)
    {
        // Filter list ==> we keep only the matching item
        $extractedItems = array_filter(iterator_to_array($list), function ($item) use ($id) {
            if (method_exists($item, 'getId') === false) {
                throw new \RuntimeException(sprintf('Method getId not found on object "%s"', get_class($item)));
            }
            if ($item->getId() === $id) {
                return true;
            }
            return false;
        });

        // We should keep exactly one item, if we have more or less than one user there's something wrong
        if (count($extractedItems) !== 1) {
            throw new \RuntimeException('We could not find the wanted item');
        }
        $item = current($extractedItems);

        return $item;
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
