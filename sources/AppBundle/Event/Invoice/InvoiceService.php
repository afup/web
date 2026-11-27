<?php

declare(strict_types=1);

namespace AppBundle\Event\Invoice;

use AppBundle\Event\Model\Invoice;
use AppBundle\Event\Model\Repository\InvoiceRepository;
use AppBundle\Event\Entity\Repository\InscriptionRepository;
use AppBundle\Event\Entity\Inscription;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use AppBundle\Event\Enum\InscriptionEtat;
use AppBundle\Event\Enum\InscriptionFacturation;

class InvoiceService
{
    public function __construct(
        private readonly InvoiceRepository $invoiceRepository,
        private readonly InscriptionRepository $inscriptionRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger,
    ) {}

    /**
     * Enregistre une commande et ses inscriptions.
     *
     * La facture (Ting) et les inscriptions (Doctrine) n'utilisent pas la même connexion :
     * si l'enregistrement des inscriptions échoue, la facture est supprimée pour ne pas
     * laisser de commande sans inscription.
     */
    public function saveWithTickets(Invoice $invoice): void
    {
        $totalAmount = 0.0;
        $inscriptions = [];

        /** @var Inscription $inscription */
        foreach ($invoice->getTickets() as $inscription) {
            if ($inscription->ticketEventType === null) {
                continue;
            }

            $inscription->transportMode = null;
            $inscription->transportDistance = null;
            $inscription->reference = (string) $invoice->getReference();
            $inscription->date = new DateTime();
            $inscription->amount = $inscription->ticketEventType->price ?? 0.0;
            $inscription->status = InscriptionEtat::Creee;
            $inscription->invoiceStatus = InscriptionFacturation::AEnvoyer;
            $inscription->forumId = (int) $invoice->getForumId();
            $inscription->comments = '<tag>' . implode(';', $inscription->getTags()) . '</tag>';

            $totalAmount += $inscription->amount;
            $inscriptions[] = $inscription;
        }

        $invoice->setAmount($totalAmount);
        $this->invoiceRepository->save($invoice);

        try {
            $this->entityManager->wrapInTransaction(static function (EntityManagerInterface $entityManager) use ($inscriptions): void {
                foreach ($inscriptions as $inscription) {
                    $entityManager->persist($inscription);
                }
            });
        } catch (\Throwable $exception) {
            $this->logger->error('Échec de l\'enregistrement des inscriptions de la commande {reference}, la facture est supprimée', [
                'reference' => $invoice->getReference(),
                'exception' => $exception,
            ]);
            $this->invoiceRepository->delete($invoice);

            throw $exception;
        }
    }

    public function handleInvoicing(
        string $reference,
        $paymentType,
        $paymentInfos,
        ?\DateTime $paymentDate,
        $email,
        $company,
        $lastname,
        $firstname,
        $address,
        $zipcode,
        $city,
        $countryId,
        $eventId,
        $oldReference = null,
        $authorization = null,
        $transaction = null,
        $status = InscriptionEtat::Creee->value,
    ): void {
        $tickets = $this->inscriptionRepository->getByReference($reference);
        $amount = 0.0;
        foreach ($tickets as $ticket) {
            $amount = round($amount + $ticket->amount, 2);
        }
        $nbTickets = count($tickets);
        $oldInvoice = null;
        if ($oldReference === '') {
            $oldReference = null;
        }
        if (null !== $oldReference) {
            $oldInvoice = $this->invoiceRepository->getByReference($oldReference);
        }
        // Si la reference n'existe pas on l'ajoute sinon on la met à jour...
        if ($nbTickets === 0 || null === $oldInvoice) {
            $invoice = new Invoice();
            $invoice->setReference($reference);
            $invoice->setInvoice(false);
        } else {
            $invoice = $oldInvoice;
            $invoice->setInvoice(true);
        }
        $invoice->setAmount($amount);
        $invoice->setPaymentType($paymentType);
        $invoice->setPaymentInfos($paymentInfos);
        $invoice->setPaymentDate($paymentDate);
        $invoice->setCompany($company);
        $invoice->setLastname($lastname);
        $invoice->setFirstname($firstname);
        $invoice->setEmail($email);
        $invoice->setAddress($address);
        $invoice->setZipcode($zipcode);
        $invoice->setCity($city);
        $invoice->setCountryId($countryId);
        $invoice->setAuthorization($authorization);
        $invoice->setTransaction($transaction);
        $invoice->setStatus($status);
        $invoice->setForumId($eventId);
        $this->invoiceRepository->save($invoice);
        // Si on change de reference
        if (null !== $oldInvoice && $oldReference !== $reference) {
            $this->deleteInvoice($oldInvoice);
        }
    }

    public function markAsInvoiced(Invoice $invoice): void
    {
        if ($invoice->getInvoice()) {
            return;
        }

        $tickets = $this->inscriptionRepository->getByReference((string) $invoice->getReference());
        foreach ($tickets as $ticket) {
            $ticket->invoiceStatus = InscriptionFacturation::Envoyee;
            $this->inscriptionRepository->save($ticket);
        }

        $invoice->setInvoice(true)->setInvoiceDate(new DateTime());
        $this->invoiceRepository->save($invoice);
    }

    public function deleteInvoice(Invoice $invoice): void
    {
        $tickets = $this->inscriptionRepository->getByReference((string) $invoice->getReference());
        if (0 === count($tickets)) {
            $this->invoiceRepository->delete($invoice);

            return;
        }
        $amount = 0.0;
        foreach ($tickets as $ticket) {
            $amount = round($amount + $ticket->amount, 2);
        }
        $invoice->setAmount($amount);
        $this->invoiceRepository->save($invoice);
    }
}
