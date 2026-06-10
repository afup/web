<?php

declare(strict_types=1);

namespace AppBundle\Accounting;

use AppBundle\Afup;
use AppBundle\Accounting\Model\Invoicing;
use AppBundle\Email\Mailer\Attachment;
use AppBundle\Email\Mailer\Mailer;
use AppBundle\Email\Mailer\MailUser;
use AppBundle\Email\Mailer\Message;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class InvoicingMailer
{
    public function __construct(
        private InvoicingPdfGenerator $pdfGenerator,
        private Mailer $mailer,
        #[Autowire('%kernel.project_dir%/../htdocs/cache/')]
        private string $publicCacheDir,
    ) {}

    public function sendInvoice(Invoicing $invoicing): bool
    {
        $invoiceNumber = $invoicing->getInvoiceNumber();

        $sujet = "Facture AFUP\n";

        $corps = "Bonjour, \n\n";
        $corps .= "Veuillez trouver ci-joint la facture correspondant à la participation au forum organisé par l'AFUP.\n";
        $corps .= "Nous restons à votre disposition pour toute demande complémentaire.\n\n";
        $corps .= "Le bureau\n\n";
        $corps .= Afup::RAISON_SOCIALE . "\n";
        $corps .= Afup::ADRESSE . "\n";
        $corps .= Afup::CODE_POSTAL . ' ' . Afup::VILLE . "\n";

        $cheminFacture = $this->publicCacheDir . 'fact' . $invoiceNumber . '.pdf';
        $this->pdfGenerator->generateInvoice($invoicing, $cheminFacture);

        $message = new Message(
            $sujet,
            new MailUser(MailUser::DEFAULT_SENDER_EMAIL, MailUser::DEFAULT_SENDER_NAME),
            new MailUser($invoicing->getEmail(), $invoicing->getLastname()),
        );
        $message->addAttachment(new Attachment(
            $cheminFacture,
            'facture-' . $invoiceNumber . '.pdf',
            'base64',
            'application/pdf',
        ));
        $message->setContent($corps);
        $ok = $this->mailer->send($message);

        @unlink($cheminFacture);

        return $ok;
    }
}
