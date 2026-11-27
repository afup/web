<?php

declare(strict_types=1);

namespace AppBundle\Tests\Accounting\Form;

use Afup\Site\Utils\Pays;
use AppBundle\Accounting\Entity\Invoicing;
use AppBundle\Accounting\Form\InvoicingRowType;
use AppBundle\Accounting\Form\QuotationType;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;

/**
 * Les contraintes du devis sont portées par l'entité Invoicing, sauf celles propres à cet
 * écran : le numéro de devis n'est obligatoire qu'en modification.
 */
#[AllowMockObjectsWithoutExpectations] // TypeTestCase mocke l'EventDispatcher en interne
class QuotationTypeTest extends TypeTestCase
{
    protected function getExtensions(): array
    {
        $pays = $this->createMock(Pays::class);
        $pays->method('obtenirPays')->willReturn(['FR' => 'France']);

        return [
            new PreloadedExtension([new QuotationType($pays), new InvoicingRowType()], []),
            // TypeTestCase ne la charge pas, or l'option `constraints` en dépend.
            new ValidatorExtension(Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator()),
        ];
    }

    /**
     * À la création, le numéro de devis est généré après la validation : l'entité ne doit
     * pas l'exiger.
     */
    #[Test]
    public function unDevisPeutEtreCreeSansNumero(): void
    {
        $quotation = $this->newQuotation();
        $form = $this->factory->create(QuotationType::class, $quotation);

        $form->submit($this->payload());

        self::assertTrue($form->isValid(), (string) $form->getErrors(true, false));
        self::assertSame('', $quotation->numeroDevis);
        self::assertSame('ACME', $quotation->societe);
    }

    #[Test]
    public function leNumeroDeDevisEstObligatoireEnModification(): void
    {
        $quotation = $this->newQuotation();
        $quotation->numeroDevis = '2026-001';
        $form = $this->factory->create(QuotationType::class, $quotation, ['actionType' => 'edit']);

        $form->submit(array_merge($this->payload(), ['numeroDevis' => '']));

        self::assertFalse($form->isValid());
        self::assertCount(1, $form->get('numeroDevis')->getErrors());
    }

    #[Test]
    public function uneSocieteVideEstRefuseeSousLeChamp(): void
    {
        $form = $this->factory->create(QuotationType::class, $this->newQuotation());

        $form->submit(array_merge($this->payload(), ['societe' => '']));

        self::assertFalse($form->isValid());
        self::assertCount(1, $form->get('societe')->getErrors());
        self::assertCount(0, $form->getErrors());
    }

    #[Test]
    public function uneVilleTropLongueEstRefuseeSousLeChamp(): void
    {
        $form = $this->factory->create(QuotationType::class, $this->newQuotation());

        $form->submit(array_merge($this->payload(), ['ville' => str_repeat('a', 51)]));

        self::assertFalse($form->isValid());
        self::assertCount(1, $form->get('ville')->getErrors());
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(): array
    {
        return [
            'dateDevis' => '2026-01-15',
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
            'details' => [
                ['reference' => 'SPONSO', 'designation' => 'Sponsoring bronze', 'quantite' => '1', 'prixUnitaire' => '1000', 'tva' => '20'],
            ],
        ];
    }

    private function newQuotation(): Invoicing
    {
        $quotation = new Invoicing();
        $quotation->dateDevis = new \DateTime('2026-01-15');
        $quotation->idPays = 'FR';

        return $quotation;
    }
}
