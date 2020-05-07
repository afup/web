<?php

namespace AppBundle\Controller\Admin\Membership;

use AppBundle\Association\Form\UserType;
use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\CompanyMemberInvitation;
use AppBundle\Association\Model\Repository\CompanyMemberInvitationRepository;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;

class MemberInvitationAction
{
    /** @var CompanyMemberInvitationRepository */
    private $companyMemberInvitationRepository;
    /** @var CompanyMemberRepository */
    private $companyMemberRepository;
    /** @var FormFactoryInterface */
    private $formFactory;
    /** @var UserRepository */
    private $userRepository;
    /** @var FlashBagInterface */
    private $flashBag;
    /** @var Environment */
    private $twig;

    public function __construct(
        CompanyMemberInvitationRepository $companyMemberInvitationRepository,
        CompanyMemberRepository $companyMemberRepository,
        FormFactoryInterface $formFactory,
        UserRepository $userRepository,
        FlashBagInterface $flashBag,
        Environment $twig
    ) {
        $this->companyMemberInvitationRepository = $companyMemberInvitationRepository;
        $this->companyMemberRepository = $companyMemberRepository;
        $this->formFactory = $formFactory;
        $this->userRepository = $userRepository;
        $this->flashBag = $flashBag;
        $this->twig = $twig;
    }

    public function __invoke(Request $request, $invitationId, $token)
    {
        /** @var CompanyMemberInvitation $invitation */
        $invitation = $this->companyMemberInvitationRepository->getOneBy([
            'id' => $invitationId,
            'token' => $token,
            'status' => CompanyMemberInvitation::STATUS_PENDING,
        ]);
        $company = null;
        if ($invitation) {
            /** @var CompanyMember $company */
            $company = $this->companyMemberRepository->get($invitation->getCompanyId());
        }
        if ($company === null) {
            throw new NotFoundHttpException(sprintf('Could not find invitation with token "%s"', $token));
        }

        $userForm = $this->formFactory->create(UserType::class);
        $userForm->handleRequest($request);
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            /** @var $user User */
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
            $this->userRepository->save($user);
            $this->companyMemberInvitationRepository->save($invitation);
            $this->flashBag->add('success', 'Votre compte a été créé !');

            return new RedirectResponse('/pages/administration/');
        }

        return new Response($this->twig->render('site/company_membership/member_invitation.html.twig', [
            'company' => $company,
            'form' => $userForm->createView(),
        ]));
    }
}
