<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Membership;

use AppBundle\Association\CompanyMembership\InvitationMail;
use AppBundle\Association\CompanyMembership\SubscriptionManagement;
use AppBundle\Association\Form\CompanyMemberType;
use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\CompanyMemberInvitation;
use AppBundle\Association\Model\Repository\CompanyMemberInvitationRepository;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;

final class CompanyAction extends AbstractController
{
    public function __construct(
        private readonly ViewRenderer $view,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly CompanyMemberRepository $companyMemberRepository,
        private readonly InvitationMail $invitationMail,
        private readonly SubscriptionManagement $subscriptionManagement,
        private readonly CompanyMemberInvitationRepository $companyMemberInvitationRepository,
    ) {}

    public function __invoke(Request $request): Response
    {
        $data = new CompanyMember();
        $data->setInvitations([
            (new CompanyMemberInvitation())->setManager(true),
        ]);

        $subscribeForm = $this->createForm(CompanyMemberType::class, $data);
        $subscribeForm->handleRequest($request);

        if ($subscribeForm->isSubmitted() && $subscribeForm->isValid()) {
            /**
             * @var CompanyMember $member
             */
            $member = $subscribeForm->getData();
            $this->companyMemberRepository->save($member);

            foreach ($member->getInvitations() as $index => $invitation) {
                if ($invitation->getEmail() === '') {
                    continue;
                }
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

                $this->companyMemberInvitationRepository->save($invitation);

                // Send mail to the other guy, begging for him to join the company
                $this->eventDispatcher->addListener(KernelEvents::TERMINATE, function () use ($member, $invitation): void {
                    $this->invitationMail->sendInvitation($member, $invitation);
                });
            }

            $subscriptionManager = $this->subscriptionManagement;
            $invoice = $subscriptionManager->createInvoiceForInscription($member, count($member->getInvitations()));

            return $this->redirectToRoute('company_membership_payment', ['invoiceNumber' => $invoice['invoice'], 'token' => $invoice['token']]);
        }

        return $this->view->render('site/company_membership/adhesion_entreprise.html.twig', [
            'form' => $subscribeForm->createView(),
        ]);
    }
}
