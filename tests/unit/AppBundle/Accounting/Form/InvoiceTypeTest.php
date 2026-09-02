<?php

declare(strict_types=1);

namespace AppBundle\Tests\Accounting\Form;

use Afup\Site\Utils\Pays;
use AppBundle\Accounting\Form\InvoiceType;
use AppBundle\Accounting\InvoicingPaymentStatus;
use AppBundle\Accounting\Entity\Invoicing;
use AppBundle\Accounting\Entity\InvoicingDetail;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\DataProvider;
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
        self::assertSame(InvoicingPaymentStatus::Payed, $invoice->etatPaiement);
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
        self::assertSame(InvoicingPaymentStatus::Payed, $invoice->etatPaiement);
    }

    /**
     * Le navigateur laisse passer un champ obligatoire rempli d'espaces, que le formulaire
     * réduit à une chaîne vide : elle doit arriver jusqu'à NotBlank, et non pas `null`
     * dans une propriété `string` (InvalidTypeException, erreur 500).
     */
    #[Test]
    #[DataProvider('champsObligatoires')]
    public function unChampObligatoireRempliDEspacesEstRefuse(string $field): void
    {
        $invoice = $this->newInvoice([$this->detail('SPONSO', 'Sponsoring bronze')]);
        $form = $this->factory->create(InvoiceType::class, $invoice);

        $form->submit(array_merge($this->payload(), [$field => '   ']));

        self::assertFalse($form->isValid());
        self::assertCount(1, $form->get($field)->getErrors());
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function champsObligatoires(): iterable
    {
        foreach (['societe', 'codePostal', 'ville', 'email'] as $field) {
            yield $field => [$field];
        }
    }

    /**
     * @return array<string, string>
     */
    private function payload(): array
    {
        return [
            'dateFacture' => '2026-01-15',
            'societe' => 'ACME',
            'service' => '',
            'adresse' => '1 rue du test',
            'codePostal' => '69000',
            'ville' => 'Lyon',
            'idPays' => 'FR',
            'nom' => '',
            'prenom' => '',
            'telephone' => '',
            'email' => 'compta@example.com',
            'tvaIntra' => '',
            'referenceClient1' => '',
            'referenceClient2' => '',
            'referenceClient3' => '',
            'observation' => '',
            'deviseFacture' => 'EUR',
            'numeroDevis' => '2026-001',
            'numeroFacture' => '2026-001',
            'etatPaiement' => (string) InvoicingPaymentStatus::Payed->value,
            'datePaiement' => '2026-02-01',
        ];
    }

    /**
     * @param list<InvoicingDetail> $details
     */
    private function newInvoice(array $details): Invoicing
    {
        $invoice = new Invoicing();
        $invoice->id = 42;
        $invoice->societe = 'ACME';
        $invoice->adresse = '1 rue du test';
        $invoice->codePostal = '69000';
        $invoice->ville = 'Lyon';
        $invoice->idPays = 'FR';
        $invoice->email = 'compta@example.com';
        $invoice->numeroDevis = '2026-001';
        $invoice->numeroFacture = '2026-001';
        $invoice->dateFacture = new \DateTime('2026-01-15');
        $invoice->etatPaiement = InvoicingPaymentStatus::Waiting;
        foreach ($details as $detail) {
            $invoice->addDetail($detail);
        }

        return $invoice;
    }

    private function detail(string $reference, string $designation): InvoicingDetail
    {
        $detail = new InvoicingDetail();
        $detail->reference = $reference;
        $detail->designation = $designation;
        $detail->quantite = 0.0;
        $detail->prixUnitaire = 0.0;
        $detail->tva = 0.0;

        return $detail;
    }
}
