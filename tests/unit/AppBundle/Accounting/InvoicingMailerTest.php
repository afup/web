<?php

declare(strict_types=1);

namespace AppBundle\Tests\Accounting;

use AppBundle\Accounting\InvoicingMailer;
use AppBundle\Accounting\InvoicingPdfGenerator;
use AppBundle\Accounting\Model\Invoicing;
use AppBundle\Email\Mailer\Mailer;
use AppBundle\Email\Mailer\Message;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class InvoicingMailerTest extends TestCase
{
    public function testSendInvoiceAddsSponsorsCc(): void
    {
        $invoicing = new Invoicing();
        $invoicing->setInvoiceNumber('2026-001');
        $invoicing->setCompany('Société');
        $invoicing->setEmail('contact@exemple.fr');
        $invoicing->setLastname('Dupont');

        /** @var Mailer&MockObject $mailer */
        $mailer = $this->createMock(Mailer::class);
        $pdfGenerator = $this->createStub(InvoicingPdfGenerator::class);

        $mailer->expects(self::once())->method('send')
            ->with(self::callback(static function (Message $message): bool {
                self::assertSame(1, count($message->getRecipients() ?? []));
                $cc = $message->getCc();
                self::assertCount(1, $cc);
                self::assertSame('sponsors@afup.org', array_key_first($cc));

                return true;
            }));

        $invoicingMailer = new InvoicingMailer($pdfGenerator, $mailer, sys_get_temp_dir() . '/');
        $invoicingMailer->sendInvoice($invoicing);
    }
}
