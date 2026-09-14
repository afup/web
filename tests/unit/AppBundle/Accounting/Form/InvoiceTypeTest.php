<?php

declare(strict_types=1);

namespace AppBundle\Tests\Accounting\Form;

use Afup\Site\Utils\Pays;
use AppBundle\Accounting\Form\InvoiceType;
use AppBundle\Accounting\InvoicingPaymentStatus;
use AppBundle\Accounting\Model\Invoicing;
use AppBundle\Accounting\Model\InvoicingDetail;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;

/**
 * L'écran facture affiche les lignes en lecture seule : il ne doit valider que ce qu'il
 * laisse modifier, sans quoi une donnée héritée bloque toute modification de la facture.
 */
#[AllowMockObjectsWithoutExpectations] // TypeTestCase mocke l'EventDispatcher en interne
class InvoiceTypeTest extends TypeTestCase
{
    protected function getExtensions(): array
    {
        $pays = $this->createMock(Pays::class);
        $pays->method('obtenirPays')->willReturn(['FR' => 'France']);

        return [
            new PreloadedExtension([new InvoiceType($pays)], []),
            // TypeTestCase ne la charge pas, or l'option `constraints` en dépend.
            new ValidatorExtension(Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator()),
        ];
    }

    #[Test]
    public function uneFacturePeutEtreMarqueePayee(): void
    {
        $invoice = $this->newInvoice([$this->detail('SPONSO', 'Sponsoring bronze')]);
        $form = $this->factory->create(InvoiceType::class, $invoice);

        $form->submit($this->payload());

        self::assertTrue($form->isValid(), (string) $form->getErrors(true, false));
        self::assertSame(InvoicingPaymentStatus::Payed, $invoice->getPaymentStatus());
    }

    /**
     * Le back-office historique créait systématiquement cinq lignes de détail, vides
     * comprises. Ces lignes ne sont pas éditables depuis l'écran facture : les valider
     * rendrait ces factures définitivement non modifiables.
     */
    #[Test]
    public function uneFactureAvecDesLignesVidesHeriteesPeutEtreMarqueePayee(): void
    {
        $invoice = $this->newInvoice([
            $this->detail('SPONSO', 'Sponsoring bronze'),
            $this->detail('', ''),
            $this->detail('', ''),
        ]);
        $form = $this->factory->create(InvoiceType::class, $invoice);

        $form->submit($this->payload());

        self::assertTrue($form->isValid(), (string) $form->getErrors(true, false));
        self::assertSame(InvoicingPaymentStatus::Payed, $invoice->getPaymentStatus());
    }

    /**
     * @return array<string, string>
     */
    private function payload(): array
    {
        return [
            'invoiceDate' => '2026-01-15',
            'company' => 'ACME',
            'service' => '',
            'address' => '1 rue du test',
            'zipcode' => '69000',
            'city' => 'Lyon',
            'countryId' => 'FR',
            'lastname' => '',
            'firstname' => '',
            'phone' => '',
            'email' => 'compta@example.com',
            'tvaIntra' => '',
            'refClt1' => '',
            'refClt2' => '',
            'refClt3' => '',
            'observation' => '',
            'currency' => 'EUR',
            'quotationNumber' => '2026-001',
            'invoiceNumber' => '2026-001',
            'paymentStatus' => (string) InvoicingPaymentStatus::Payed->value,
            'paymentDate' => '2026-02-01',
        ];
    }

    /**
     * @param list<InvoicingDetail> $details
     */
    private function newInvoice(array $details): Invoicing
    {
        $invoice = new Invoicing();
        $invoice->setId(42);
        $invoice->setCompany('ACME');
        $invoice->setAddress('1 rue du test');
        $invoice->setZipcode('69000');
        $invoice->setCity('Lyon');
        $invoice->setCountryId('FR');
        $invoice->setEmail('compta@example.com');
        $invoice->setQuotationNumber('2026-001');
        $invoice->setInvoiceNumber('2026-001');
        $invoice->setInvoiceDate(new \DateTime('2026-01-15'));
        $invoice->setPaymentStatus(InvoicingPaymentStatus::Waiting);
        $invoice->setDetails($details);

        return $invoice;
    }

    private function detail(string $reference, string $designation): InvoicingDetail
    {
        $detail = new InvoicingDetail();
        $detail->setReference($reference);
        $detail->setDesignation($designation);
        $detail->setQuantity(0.0);
        $detail->setUnitPrice(0.0);
        $detail->setTva(0.0);

        return $detail;
    }
}
