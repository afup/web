<?php

declare(strict_types=1);

namespace AppBundle\Event\Invoice;

use AppBundle\Email\Mailer\Attachment;
use AppBundle\Email\Mailer\Mailer;
use AppBundle\Email\Mailer\MailUser;
use AppBundle\Email\Mailer\MailUserFactory;
use AppBundle\Email\Mailer\Message;
use AppBundle\Event\Model\Repository\InvoiceRepository;

class EventInvoiceMailer
{
    public function __construct(
        private readonly EventInvoicePdfGenerator $pdfGenerator,
        private readonly InvoiceRepository $invoiceRepository,
        private readonly Mailer $mailer,
        private readonly InvoiceService $invoiceService,
    ) {}

    public function send(string $reference, bool $copyTresorier = true, bool $facturer = true): bool
    {
        $invoice = $this->invoiceRepository->getByReference($reference);
        if ($invoice === null) {
            return false;
        }

        $cheminFacture = AFUP_CHEMIN_RACINE . 'cache' . DIRECTORY_SEPARATOR . 'fact' . $reference . '.pdf';
        $this->pdfGenerator->generateInvoice($reference, $cheminFacture);

        $message = new Message(
            'Facture évènement AFUP',
            MailUserFactory::afup(),
            new MailUser($invoice->getEmail(), sprintf('%s %s', $invoice->getFirstname(), $invoice->getLastname())),
        );

        $this->mailer->renderTemplate($message, 'mail_templates/facture-forum.html.twig', [
            'raison_sociale' => AFUP_RAISON_SOCIALE,
            'adresse' => AFUP_ADRESSE,
            'ville' => AFUP_CODE_POSTAL . ' ' . AFUP_VILLE,
        ]);

        $message->addAttachment(new Attachment(
            $cheminFacture,
            'facture-' . $reference . '.pdf',
            'base64',
            'application/pdf',
        ));

        if ($copyTresorier) {
            $message->addBcc(MailUserFactory::tresorier());
        }

        $ok = $this->mailer->send($message);
        @unlink($cheminFacture);

        if ($ok && $facturer) {
            $this->invoiceService->markAsInvoiced($invoice);
        }

        return $ok;
    }
}
