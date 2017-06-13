<?php

namespace AppBundle\Event\Ticket;

use AppBundle\Association\Model\User;
use AppBundle\Event\Form\PurchaseType;
use AppBundle\Event\Form\TicketType;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\InvoiceFactory;
use AppBundle\Event\Model\Ticket;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class PurchaseTypeFactory
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $securityChecker;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var InvoiceFactory
     */
    private $invoiceFactory;

    public function __construct(
        AuthorizationCheckerInterface $securityChecker,
        FormFactoryInterface $formFactory,
        InvoiceFactory $invoiceFactory
    ) {
        $this->securityChecker = $securityChecker;
        $this->formFactory = $formFactory;
        $this->invoiceFactory = $invoiceFactory;
    }

    public function getPurchaseForUser(Event $event, User $user = null)
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

        $invoice = $this->invoiceFactory->createInvoiceForEvent($event);
        $ticket = new Ticket();
        $invoice
            ->addTicket($ticket)
            ->addTicket(clone $ticket)
            ->addTicket(clone $ticket)
            ->addTicket(clone $ticket)
            ->addTicket(clone $ticket)
        ;
        $invoiceType = $this->formFactory->create(
            PurchaseType::class,
            $invoice,
            ['event_id' => $event->getId(), 'member_type' => $memberType]
        );

        return $invoiceType;
    }
}
