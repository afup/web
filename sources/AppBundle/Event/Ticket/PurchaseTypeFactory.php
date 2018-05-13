<?php

namespace AppBundle\Event\Ticket;

use AppBundle\Association\Model\User;
use AppBundle\Event\Form\PurchaseType;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\InvoiceFactory;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Ticket;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class PurchaseTypeFactory
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var InvoiceFactory
     */
    private $invoiceFactory;

    /**
     * @var SpeakerRepository
     */
    private $speakerRepository;

    /**
     * @var MembershipDiscountEligibiliityComputer
     */
    private $membershipDiscountEligibilityComputer;

    public function __construct(
        MembershipDiscountEligibiliityComputer $membershipDiscountEligibilityComputer,
        FormFactoryInterface $formFactory,
        InvoiceFactory $invoiceFactory,
        SpeakerRepository $speakerRepository
    ) {
        $this->formFactory = $formFactory;
        $this->invoiceFactory = $invoiceFactory;
        $this->speakerRepository = $speakerRepository;
        $this->membershipDiscountEligibilityComputer = $membershipDiscountEligibilityComputer;
    }

    public function getPurchaseForUser(Event $event, User $user = null, $specialPriceToken = null)
    {
        $isCfpSubmitter = null !== $user && null !== $this->speakerRepository->getByEventAndEmail($event, $user->getEmail());

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

        $invoiceType = $this->formFactory->create(
            PurchaseType::class,
            $invoice,
            [
                'event_id' => $event->getId(),
                'is_cfp_submitter' => $isCfpSubmitter,
                'special_price_token' => $specialPriceToken,
                'user_eligible_for_membership_discount' => $this->membershipDiscountEligibilityComputer->isEligibleToMembershopDiscount($user)
            ]
        );

        return $invoiceType;
    }
}
