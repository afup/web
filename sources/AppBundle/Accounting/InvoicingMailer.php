<?php

declare(strict_types=1);

namespace AppBundle\Accounting;

use Afup\Site\Utils\Mailing;
use AppBundle\Accounting\Model\Invoicing;
use AppBundle\Email\Mailer\Attachment;
use AppBundle\Email\Mailer\MailUser;
use AppBundle\Email\Mailer\Message;

class InvoicingMailer
{
    public function __construct(private readonly InvoicingPdfGenerator $pdfGenerator) {}

    public function sendInvoice(Invoicing $invoicing): bool
    {
        $invoiceNumber = $invoicing->getInvoiceNumber();

        $sujet = "Facture AFUP\n";

        $corps = "Bonjour, \n\n";
        $corps .= "Veuillez trouver ci-joint la facture correspondant à la participation au forum organisé par l'AFUP.\n";
        $corps .= "Nous restons à votre disposition pour toute demande complémentaire.\n\n";
        $corps .= "Le bureau\n\n";
        $corps .= AFUP_RAISON_SOCIALE . "\n";
        $corps .= AFUP_ADRESSE . "\n";
        $corps .= AFUP_CODE_POSTAL . ' ' . AFUP_VILLE . "\n";

        $cheminFacture = AFUP_CHEMIN_RACINE . 'cache' . DIRECTORY_SEPARATOR . 'fact' . $invoiceNumber . '.pdf';
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
        $ok = Mailing::envoyerMail($message, $corps);

        @unlink($cheminFacture);

        return $ok;
    }
}
