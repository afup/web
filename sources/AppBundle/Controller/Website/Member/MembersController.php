<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Member;

use AppBundle\Association\CompanyMembership\InvitationMail;
use AppBundle\Association\CompanyMembership\UserCompany;
use AppBundle\Association\Form\CompanyMemberInvitationType;
use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\CompanyMemberInvitation;
use AppBundle\Association\Model\Repository\CompanyMemberInvitationRepository;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use AppBundle\Model\CollectionFilter;
use AppBundle\Twig\ViewRenderer;
use Assert\Assertion;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use DateTime;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class MembersController
{
    /** @var CompanyMemberRepository */
    private $companyMemberRepository;
    /** @var UserRepository */
    private $userRepository;
    /** @var CompanyMemberInvitationRepository */
    private $companyMemberInvitationRepository;
    private ViewRenderer $view;
    /** @var FormFactoryInterface */
    private $formFactory;
    /** @var CollectionFilter */
    private $collectionFilter;
    /** @var UserCompany */
    private $userCompany;
    /** @var Security */
    private $security;
    /** @var FlashBagInterface */
    private $flashBag;
    /** @var CsrfTokenManagerInterface */
    private $csrfTokenManager;
    /** @var UrlGeneratorInterface */
    private $urlGenerator;
    /** @var InvitationMail */
    private $invitationMail;
    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(
        CompanyMemberRepository           $companyMemberRepository,
        UserRepository                    $userRepository,
        CompanyMemberInvitationRepository $companyMemberInvitationRepository,
        ViewRenderer                      $view,
        FormFactoryInterface              $formFactory,
        CollectionFilter                  $collectionFilter,
        UserCompany                       $userCompany,
        Security                          $security,
        FlashBagInterface                 $flashBag,
        CsrfTokenManagerInterface         $csrfTokenManager,
        UrlGeneratorInterface             $urlGenerator,
        InvitationMail                    $invitationMail,
        EventDispatcherInterface          $eventDispatcher
    ) {
        $this->companyMemberRepository = $companyMemberRepository;
        $this->userRepository = $userRepository;
        $this->companyMemberInvitationRepository = $companyMemberInvitationRepository;
        $this->view = $view;
        $this->formFactory = $formFactory;
        $this->collectionFilter = $collectionFilter;
        $this->security = $security;
        $this->userCompany = $userCompany;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->urlGenerator = $urlGenerator;
        $this->invitationMail = $invitationMail;
        $this->eventDispatcher = $eventDispatcher;
        $this->flashBag = $flashBag;
    }

    public function __invoke(Request $request)
    {
        $id = $request->get('id');
        if ($id && $this->security->isGranted('ROLE_SUPER_ADMIN')) {
            $companyId = $id;
        } else {
            /** @var User $user */
            $user = $this->security->getUser();
            Assertion::isInstanceOf($user, User::class);
            $companyId = $user->getCompanyId();
        }
        /** @var CompanyMember $company */
        $company = $this->companyMemberRepository->get($companyId);
        if ($company === null) {
            throw new NotFoundHttpException('Company not found');
        }

        $users = $this->userRepository->loadActiveUsersByCompany($company);
        $pendingInvitations = $this->companyMemberInvitationRepository->loadPendingInvitationsByCompany($company);

        $invitation = new CompanyMemberInvitation();
        $invitationForm = $this->formFactory->create(CompanyMemberInvitationType::class, $invitation);
        $invitationForm->handleRequest($request);
        $canAddUser = $pendingInvitations->count() + $users->count() < $company->getMaxMembers();
        if ($request->isMethod(Request::METHOD_POST)) {
            if ($invitationForm->isSubmitted() && $invitationForm->isValid()) {
                if ($canAddUser) {
                    $this->addUser($company, $invitation, $users, $pendingInvitations);
                } else {
                    $this->flashBag->add('error', 'Vous avez atteint le nombre maximum de membres');
                }
            } elseif (!$this->csrfTokenManager->isTokenValid(new CsrfToken('member_company_members', $request->request->get('token')))) {
                $this->flashBag->add('error', 'Erreur lors de la soumission du formulaire (jeton CSRF invalide). Merci de réessayer.');
            } elseif ($request->request->has('delete_invitation')) {
                $this->removeInvitation($request->request->get('delete_invitation'), $pendingInvitations);
            } elseif ($request->request->has('resend_invitation')) {
                $this->resendInvitation($request->request->get('resend_invitation'), $pendingInvitations, $company);
            } elseif ($request->request->has('promote_up')) {
                $this->promoteUser($request->request->get('promote_up'), $users);
            } elseif ($request->request->has('promote_down')) {
                $this->disproveUser($request->request->get('promote_down'), $users);
            } elseif ($request->request->has('remove')) {
                $this->removeUser($request->request->get('remove'), $users);
            }

            return new RedirectResponse($this->urlGenerator->generate('member_company_members', [
                'id' => $this->security->isGranted('ROLE_SUPER_ADMIN') ? $companyId : null,
            ]));
        }

        return $this->view->render('admin/association/membership/members_company.html.twig', [
            'title' => 'Les membres de mon entreprise',
            'users' => $users,
            'invitations' => $pendingInvitations,
            'formInvitation' => $invitationForm->createView(),
            'company' => $company,
            'canAddUser' => $canAddUser,
            'token' => $this->csrfTokenManager->getToken('member_company_members'),
        ]);
    }

    private function addUser(
        CompanyMember $company,
        CompanyMemberInvitation $invitation,
        CollectionInterface $users,
        CollectionInterface $pendingInvitations
    ): void {
        // Check if there is already a pending invitation for this email and this company
        $matchingUser = $this->collectionFilter->findOne($users, 'getEmail', $invitation->getEmail());
        $matchingInvitation = $this->collectionFilter->findOne($pendingInvitations, 'getEmail', $invitation->getEmail());

        if ($matchingInvitation !== null || $matchingUser !== null) {
            $this->flashBag->add('error', 'Vous ne pouvez pas envoyer plusieurs invitations au même email.');

            return;
        }

        // Handle invitation
        $invitation
            ->setSubmittedOn(new DateTime())
            ->setCompanyId($company->getId())
            ->setToken(base64_encode(random_bytes(30)))
            ->setStatus(CompanyMemberInvitation::STATUS_PENDING);
        $this->companyMemberInvitationRepository->save($invitation);
        // Send mail to the other guy, begging for him to join the company
        $this->eventDispatcher->addListener(KernelEvents::TERMINATE, function () use ($company, $invitation): void {
            $this->invitationMail->sendInvitation($company, $invitation);
        });
        $this->flashBag->add('notice', sprintf('L\'invitation a été envoyée à l\'adresse %s.', $invitation->getEmail()));
    }

    private function removeInvitation($emailToDelete, CollectionInterface $pendingInvitations): void
    {
        /** @var CompanyMemberInvitation $invitationToDelete */
        $invitationToDelete = $this->collectionFilter->findOne($pendingInvitations, 'getEmail', $emailToDelete);
        if ($invitationToDelete !== null) {
            $invitationToDelete->setStatus(CompanyMemberInvitation::STATUS_CANCELLED);
            $this->companyMemberInvitationRepository->save($invitationToDelete);
            $this->flashBag->add('notice', 'L\'invitation a été annulée.');
        } else {
            $this->flashBag->add('error', 'Une erreur a été rencontrée lors de la suppression de cette invitation');
        }
    }

    private function resendInvitation($emailToSend, CollectionInterface $pendingInvitations, CompanyMember $company): void
    {
        /** @var CompanyMemberInvitation $invitationToSend */
        $invitationToSend = $this->collectionFilter->findOne($pendingInvitations, 'getEmail', $emailToSend);
        if ($invitationToSend !== null) {
            $this->invitationMail->sendInvitation($company, $invitationToSend);
            $this->flashBag->add('notice', 'L\'invitation a été renvoyée.');
        } else {
            $this->flashBag->add('error', 'Une erreur est survenue lors de l\'envoi de l\'invitation');
        }
    }

    private function promoteUser($emailToPromote, CollectionInterface $users): void
    {
        /** @var User $user */
        $user = $this->collectionFilter->findOne($users, 'getEmail', $emailToPromote);
        if ($user !== null) {
            $this->userCompany->setManager($user);
            $this->flashBag->add('notice', 'Le membre a été promu en tant que manager.');
        } else {
            $this->flashBag->add('error', 'Une erreur est survenue lors de l\'ajout des droits de gestion à ce membre');
        }
    }

    private function disproveUser($emailToDisapprove, CollectionInterface $users): void
    {
        /** @var User $user */
        $user = $this->collectionFilter->findOne($users, 'getEmail', $emailToDisapprove);
        /** @var User $connectedUser */
        $connectedUser = $this->security->getUser();
        Assertion::notNull($connectedUser);

        if ($user === null) {
            $this->flashBag->add('error', 'Une erreur est survenue lors de la suppression des droits de gestion de ce membre');
        } elseif ($user->getId() === $connectedUser->getId()) {
            $this->flashBag->add('error', 'Vous ne pouvez pas enlever vos droits de gestion');
        } else {
            $this->userCompany->unsetManager($user);
            $this->flashBag->add('notice', 'Le membre n\'a plus accès la gestion de l\'entreprise.');
        }
    }

    private function removeUser($emailToRemove, CollectionInterface $users): void
    {
        /** @var User $user */
        $user = $this->collectionFilter->findOne($users, 'getEmail', $emailToRemove);
        /** @var User $connectedUser */
        $connectedUser = $this->security->getUser();
        Assertion::notNull($connectedUser);

        if ($user === null) {
            $this->flashBag->add('error', 'Une erreur est survenue lors de la suppression de ce compte');
        } elseif ($user->getId() === $connectedUser->getId()) {
            $this->flashBag->add('error', 'Vous ne pouvez pas supprimer votre propre compte');
        } else {
            $this->userCompany->disableUser($user);
            $this->flashBag->add('notice', 'Le compte a été supprimé de votre adhésion entreprise.');
        }
    }
}
