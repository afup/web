<?php

declare(strict_types=1);

namespace AppBundle\Event\Ticket;

use AppBundle\Association\Model\User;
use AppBundle\Event\Form\PurchaseType;
use AppBundle\Event\Form\TicketType;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\InvoiceFactory;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Ticket;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class PurchaseTypeFactory
{
    private AuthorizationCheckerInterface $securityChecker;

    private FormFactoryInterface $formFactory;

    private InvoiceFactory $invoiceFactory;

    private SpeakerRepository $speakerRepository;

    public function __construct(
        AuthorizationCheckerInterface $securityChecker,
        FormFactoryInterface $formFactory,
        InvoiceFactory $invoiceFactory,
        SpeakerRepository $speakerRepository
    ) {
        $this->securityChecker = $securityChecker;
        $this->formFactory = $formFactory;
        $this->invoiceFactory = $invoiceFactory;
        $this->speakerRepository = $speakerRepository;
    }

    public function getPurchaseForUser(Event $event, User $user = null, $specialPriceToken = null)
    {
        $memberType = TicketType::MEMBER_NOT;

        if (
            $this->securityChecker->isGranted('ROLE_USER', $user)
            &&
            $user->hasRole('ROLE_MEMBER_EXPIRED') === false
        ) {
            if ($user->getCompanyId() > 0) {
                $memberType = TicketType::MEMBER_CORPORATE;
            } else {
                $memberType = TicketType::MEMBER_PERSONAL;
            }
        }

        $isCfpSubmitter = $user instanceof User && $this->speakerRepository->hasCFPSubmitted($event, $user->getEmail());

        $invoice = $this->invoiceFactory->createInvoiceForEvent($event);
        $ticket = new Ticket();

        if (null !== $specialPriceToken) {
            $ticket->setSpecialPriceToken($specialPriceToken);
            $invoice->addTicket(clone $ticket);
        } else {
            for ($i=1; $i<=PurchaseType::MAX_NB_PERSONNES; $i++) {
                $invoice->addTicket(clone $ticket);
            }
        }

        return $this->formFactory->create(
            PurchaseType::class,
            $invoice,
            ['event_id' => $event->getId(), 'member_type' => $memberType, 'is_cfp_submitter' => $isCfpSubmitter, 'special_price_token' => $specialPriceToken]
        );
    }
}
