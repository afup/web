<?php


namespace AppBundle\Controller;


use Afup\Site\Association\Cotisations;
use Afup\Site\Utils\Mail;
use AppBundle\Association\Form\CompanyMemberType;
use AppBundle\Association\Form\UserType;
use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\CompanyMemberInvitation;
use AppBundle\Association\Model\Repository\CompanyMemberInvitationRepository;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Association\Model\Repository\SubscriptionReminderLogRepository;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        $subscribeForm = $this->createForm(CompanyMemberType::class);
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

            foreach($member->getInvitations() as $index => $invitation) {
                $invitation
                    ->setSubmittedOn(new \DateTime())
                    ->setCompanyId($member->getId())
                    ->setToken(base64_encode(random_bytes(30)))
                    ->setStatus(CompanyMemberInvitation::STATUS_PENDING)
                ;
                if ($index === 0) {
                    // By security, force first employee to be defined as a manager
                    $invitation->setManager(true);
                }

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
                            'title' => sprintf("%s vous à profiter de son compte \"Membre AFUP\"", $member->getCompanyName())
                        ],
                        ['subject' => sprintf("%s vous à profiter de son compte \"Membre AFUP\"", $member->getCompanyName())]
                    );
                });
            }

            $subscriptionManager = $this->get('app.company_subscription');
            $invoice = $subscriptionManager->createInvoiceForInscription($member, count($member->getInvitations()));

            return $this->redirectToRoute('company_membership_payment', ['invoiceNumber' => $invoice['invoice'], 'token' => $invoice['token']]);
        }

        return $this->render(':site/company_membership:adhesion_entreprise.html.twig', ['form' => $subscribeForm->createView()]);
    }

    public function paymentAction($invoiceNumber, $token)
    {
        /**
         * @var $subscription Cotisations
         */
        $subscription = $this->get('app.legacy_model_factory')->createObject(Cotisations::class);
        $invoice = $subscription->getByInvoice($invoiceNumber, $token);
        /**
         * @var $company CompanyMember
         */
        $company = $this->get('ting')->get(CompanyMemberRepository::class)->get($invoice['id_personne']);

        if (!$invoice || $company === null) {
            throw $this->createNotFoundException(sprintf('Could not find the invoice "%s" with token "%s"', $invoiceNumber, $token));
        }

        $paybox = $this->get('app.paybox_factory')->createPayboxForSubscription(
            'F' . $invoiceNumber,
            (float)$invoice['montant'],
            $company->getEmail()
        );

        return $this->render(':site/company_membership:payment.html.twig', [
            'paybox' => $paybox,
            'invoice' => $invoice,
            'rib' => $this->legacyConfiguration->obtenir('rib'),
            'afup' => $this->legacyConfiguration->obtenir('afup')
        ]);
    }

    public function invoiceAction($invoiceNumber, $token)
    {
        /**
         * @var $subscription Cotisations
         */
        $subscription = $this->get('app.legacy_model_factory')->createObject(Cotisations::class);
        $invoice = $subscription->getByInvoice($invoiceNumber, $token);

        if (!$invoice) {
            throw $this->createNotFoundException(sprintf('Could not find the invoice "%s" with token "%s"', $invoiceNumber, $token));
        }

        ob_start();
        $subscription->genererFacture($invoice['id']);
        $pdf = ob_get_clean();

        $response = new Response($pdf);
        $response->headers->set('Content-Type', 'application/pdf');

        return $response;
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

        return $this->render(':admin/relances:liste.html.twig', ['logs' => $results, 'limit' => $limit, 'page' => $page]);
    }
}
