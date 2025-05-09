<?php

declare(strict_types=1);


namespace AppBundle\Payment;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Invoice;
use Symfony\Component\Routing\RouterInterface;

class PayboxFactory
{
    public function __construct(
        private readonly RouterInterface $router,
        private $payboxDomainServer,
        private $payboxSecretKey,
        private $payboxSite,
        private $payboxRang,
        private $payboxIdentifiant,
    ) {
    }

    /**
     * @param string $facture Facture id
     * @param float $montant Amount to pay
     * @param string $email Email of the company
     *
     * @return string html with payment button
     */
    public function createPayboxForSubscription($facture, $montant, $email, PayboxBilling $billing): string
    {
        $paybox = $this->getPaybox();

        $now = new \DateTime();

        $paybox
            ->setTotal($montant * 100) // Total de la commande, en centimes d'euros
            ->setCmd($facture) // Référence de la commande
            ->setPorteur($email) // Email du client final (Le porteur de la carte)
            ->setUrlRetourEffectue($this->router->generate('membership_payment_redirect', ['type'=>'success'], RouterInterface::ABSOLUTE_URL))
            ->setUrlRetourRefuse($this->router->generate('membership_payment_redirect', ['type'=>'refused'], RouterInterface::ABSOLUTE_URL))
            ->setUrlRetourAnnule($this->router->generate('membership_payment_redirect', ['type'=>'canceled'], RouterInterface::ABSOLUTE_URL))
            ->setUrlRetourErreur($this->router->generate('membership_payment_redirect', ['type'=>'error'], RouterInterface::ABSOLUTE_URL))
            ->setUrlRepondreA($this->router->generate('membership_payment', [], RouterInterface::ABSOLUTE_URL))
        ;

        return $paybox->generate($now, $billing);
    }

    public function createPayboxForTicket(Invoice $invoice, Event $event, $amount): string
    {
        $paybox = $this->getPaybox();

        $now = new \DateTime();

        $returnUrl = $this->router->generate('ticket_paybox_redirect', ['eventSlug' => $event->getPath()], RouterInterface::ABSOLUTE_URL);
        $ipnUrl = $this->router->generate('ticket_paybox_callback', ['eventSlug' => $event->getPath()], RouterInterface::ABSOLUTE_URL);

        $paybox
            ->setTotal($amount * 100) // Total de la commande, en centimes d'euros
            ->setCmd($invoice->getReference()) // Référence de la commande
            ->setPorteur($invoice->getEmail()) // Email du client final (Le porteur de la carte)
            ->setUrlRetourEffectue($returnUrl)
            ->setUrlRetourRefuse($returnUrl)
            ->setUrlRetourAnnule($returnUrl)
            ->setUrlRepondreA($ipnUrl)
        ;

        return $paybox->generate($now, PayboxBilling::createFromInvoice($invoice));
    }

    public function getPaybox(): Paybox
    {
        return new Paybox(
            $this->payboxDomainServer,
            $this->payboxSecretKey,
            $this->payboxSite,
            $this->payboxRang,
            $this->payboxIdentifiant
        );
    }
}
