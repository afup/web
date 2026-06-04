<?php

declare(strict_types=1);

namespace AppBundle\Event\Invoice;

use AppBundle\Afup;
use AppBundle\Email\Mailer\Attachment;
use AppBundle\Email\Mailer\Mailer;
use AppBundle\Email\Mailer\MailUser;
use AppBundle\Email\Mailer\MailUserFactory;
use AppBundle\Email\Mailer\Message;
use AppBundle\Event\Model\Repository\InvoiceRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class EventInvoiceMailer
{
    public function __construct(
        private readonly EventInvoicePdfGenerator $pdfGenerator,
        private readonly InvoiceRepository $invoiceRepository,
        private readonly Mailer $mailer,
        private readonly InvoiceService $invoiceService,
        #[Autowire('%kernel.project_dir%/../htdocs/cache/')]
        private readonly string $publicCacheDir,
    ) {}

    public function send(string $reference): bool
    {
        $invoice = $this->invoiceRepository->getByReference($reference);
        if ($invoice === null) {
            return false;
        }

        $cheminFacture = $this->publicCacheDir . 'fact' . $reference . '.pdf';
        $this->pdfGenerator->generateInvoice($reference, $cheminFacture);

        $message = new Message(
            'Facture évènement AFUP',
            MailUserFactory::afup(),
            new MailUser($invoice->getEmail(), sprintf('%s %s', $invoice->getFirstname(), $invoice->getLastname())),
        );

        $this->mailer->renderTemplate($message, 'mail_templates/facture-forum.html.twig', [
            'raison_sociale' => Afup::RAISON_SOCIALE,
            'adresse' => Afup::ADRESSE,
            'ville' => Afup::CODE_POSTAL . ' ' . Afup::VILLE,
        ]);

        $message->addAttachment(new Attachment(
            $cheminFacture,
            'facture-' . $reference . '.pdf',
            'base64',
            'application/pdf',
        ));
        $message->addBcc(MailUserFactory::tresorier());

        $ok = $this->mailer->send($message);
        @unlink($cheminFacture);

        if ($ok) {
            $this->invoiceService->markAsInvoiced($invoice);
        }

        return $ok;
    }
}
