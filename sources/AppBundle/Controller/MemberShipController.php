<?php


namespace AppBundle\Controller;


use Afup\Site\Utils\Mail;
use AppBundle\Association\Form\CompanyMemberType;
use AppBundle\Association\Form\UserType;
use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\CompanyMemberInvitation;
use AppBundle\Association\Model\Repository\CompanyMemberInvitationRepository;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MemberShipController extends SiteBaseController
{
    public function becomeMemberAction()
    {
        return $this->render(':site:become_member.html.twig');
    }

    public function companyAction(Request $request)
    {
        $member = new CompanyMember();
        $member->setInvitations([new CompanyMemberInvitation(), new CompanyMemberInvitation()]);
        $subscribeForm = $this->createForm(CompanyMemberType::class, $member);

        $subscribeForm->handleRequest($request);

        if ($subscribeForm->isSubmitted() && $subscribeForm->isValid()) {
            /**
             * @var $member CompanyMember
             */
            $member = $subscribeForm->getData();
            $this->get('ting')->get(CompanyMemberRepository::class)->save($member);
            /**
             * @var $invitationRepository CompanyMemberInvitationRepository
             */
            $invitationRepository = $this->get('ting')->get(CompanyMemberInvitationRepository::class);
            foreach($member->getInvitations() as $invitation) {
                $invitation
                    ->setSubmittedOn(new \DateTime())
                    ->setCompanyId($member->getId())
                    ->setToken(base64_encode(random_bytes(30)))
                    ->setStatus(CompanyMemberInvitation::STATUS_PENDING)
                ;

                $invitationRepository->save($invitation);

                // Send mail to the other guy, begging for him to join the talk
                $this->get('event_dispatcher')->addListener(KernelEvents::TERMINATE, function() use ($member, $invitation){
                    $text = $this->get('translator')->trans('mail.invitationMembership.text',
                        [
                            '%firstname%' => $member->getFirstName(),
                            '%lastname%' => $member->getLastName(),
                            '%link%' =>$this->generateUrl(
                                'company_invitation',
                                ['invitationId' => $invitation->getId(), 'token' => $invitation->getToken()],
                                UrlGeneratorInterface::ABSOLUTE_URL
                            )
                        ]
                    );

                    $mail = new Mail();
                    $mail->send(
                        'message-transactionnel-afup-org',
                        ['email' => $invitation->getEmail()],
                        [
                            'content' => $text,
                            'title' => 'test'
                        ]
                    );
                    //$mail->sendSimpleMessage('TODO OBJET MANQUANT', $text, [['email' => $invitation->getEmail()]]);
                });

                // It's the time to pay !
                // But I don't know how I'm supposed to do that... So I'll leave things here.
                // Perhaps if I have a @todo tag, sometimes it will magically work.

                // I should create an invoice? So I can create a payment link for this invoice ?

            }
        }

        return $this->render(':site:adhesion_entreprise.html.twig', ['form' => $subscribeForm->createView()]);
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
                ->setPassword(md5($user->getPassword())) /** @TODO We should change that */
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

        return $this->render('site/member_invitation.html.twig', ['company' => $company, 'form' => $userForm->createView()]);
    }
}
