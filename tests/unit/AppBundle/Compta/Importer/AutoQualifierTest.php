<?php

declare(strict_types=1);

namespace AppBundle\Tests\Compta\Importer;

use AppBundle\Accounting\Entity\Category;
use AppBundle\Accounting\Entity\Event;
use AppBundle\Accounting\Entity\Rule;
use AppBundle\Accounting\OperationType;
use AppBundle\Compta\Importer\AutoQualifier;
use AppBundle\Compta\Importer\Operation;
use AppBundle\Model\ComptaCategorie;
use AppBundle\Model\ComptaEvenement;
use AppBundle\Model\ComptaModeReglement;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AutoQualifierTest extends TestCase
{
    public function testDefaultOperation(): void
    {
        $operation = new Operation('2022-02-22', 'DESCRIPTION', 123, OperationType::Credit, '1234');
        $qualifier = new AutoQualifier([]);
        $actual = $qualifier->qualify($operation);

        self::assertEquals('2022-02-22', $actual['date_ecriture']);
        self::assertEquals(2, $actual['idoperation']);
        self::assertEquals('123', $actual['montant']);
        self::assertEquals(AutoQualifier::DEFAULT_CATEGORIE, $actual['categorie']);
        self::assertEquals(AutoQualifier::DEFAULT_EVENEMENT, $actual['evenement']);
        self::assertEquals(AutoQualifier::DEFAULT_REGLEMENT, $actual['idModeReglement']);
        self::assertEquals(AutoQualifier::DEFAULT_ATTACHMENT, $actual['attachmentRequired']);
        self::assertEquals('DESCRIPTION', $actual['description']);
    }

    public static function idModeReglementData(): array
    {
        return [
            'Défaut' => ['XXX blablabla', AutoQualifier::DEFAULT_REGLEMENT],
            'Autre' => ['Blablabla CHE', AutoQualifier::DEFAULT_REGLEMENT],
            'Chèque' => ['CHE blablabla', ComptaModeReglement::CHEQUE],
            'Chèque remise' => ['REM blablabla', ComptaModeReglement::CHEQUE],
            'CB' => ['CB blablabla', ComptaModeReglement::CB],
            'VIR' => ['VIR blablabla', ComptaModeReglement::VIREMENT],
            'PRLV' => ['PRLV blablabla', ComptaModeReglement::PRELEVEMENT],
        ];
    }

    #[DataProvider('idModeReglementData')]
    public function testIdModeReglement($description, $idModeReglement): void
    {
        $operation = new Operation('2022-02-22', $description, 123, OperationType::Credit, '1234');
        $qualifier = new AutoQualifier([]);
        $actual = $qualifier->qualify($operation);

        self::assertEquals($idModeReglement, $actual['idModeReglement']);
    }


    public static function qualifierData(): array
    {
        return [
            'sprd.net' => ['VIR SEPA sprd.net AG blablabla', OperationType::Credit,
                ComptaModeReglement::VIREMENT, ComptaEvenement::ASSOCIATION_AFUP, ComptaCategorie::GOODIES, 1, 100, 'montant_ht_soumis_tva_0'],
            'COM AFUP' => ['*CB COM AFUP blablabla', OperationType::Debit,
                ComptaModeReglement::PRELEVEMENT, ComptaEvenement::GESTION, ComptaCategorie::FRAIS_DE_COMPTE, AutoQualifier::DEFAULT_ATTACHMENT, 94.79, 'montant_ht_soumis_tva_5_5'],
            'COTIS ASSOCIATIS' => ['* COTIS ASSOCIATIS ESSENTIEL blablabla', OperationType::Debit,
                ComptaModeReglement::PRELEVEMENT, ComptaEvenement::GESTION, ComptaCategorie::FRAIS_DE_COMPTE, AutoQualifier::DEFAULT_ATTACHMENT, 90.91, 'montant_ht_soumis_tva_10'],
            'URSSAF' => ['PRLV URSSAF blablabla', OperationType::Debit,
                ComptaModeReglement::PRELEVEMENT, ComptaEvenement::GESTION, ComptaCategorie::CHARGES_SOCIALES, AutoQualifier::DEFAULT_ATTACHMENT, 83.33, 'montant_ht_soumis_tva_20'],
            'DGFIP' => ['PRLV B2B DGFIP', OperationType::Debit,
                ComptaModeReglement::PRELEVEMENT, ComptaEvenement::GESTION, ComptaCategorie::PRELEVEMENT_SOURCE, AutoQualifier::DEFAULT_ATTACHMENT, null, null],
            'RETRAITE' => ['PRLV A3M - RETRAITE - MALAKOFF HUMANIS blablabla', OperationType::Debit,
                ComptaModeReglement::PRELEVEMENT, ComptaEvenement::GESTION, ComptaCategorie::CHARGES_SOCIALES, AutoQualifier::DEFAULT_ATTACHMENT, null, null],
            'Online.net' => ['PRLV Online SAS - blablabla', OperationType::Debit,
                ComptaModeReglement::PRELEVEMENT, ComptaEvenement::ASSOCIATION_AFUP, ComptaCategorie::OUTILS, 1, null, null],
            'meetup.org' => ['CB MEETUP ORG blablabla', OperationType::Debit,
                ComptaModeReglement::CB, ComptaEvenement::ASSOCIATION_AFUP, ComptaCategorie::MEETUP, 1, null, null],
            'POINT TRANSACTION' => ['PRLV POINT TRANSACTION SYSTEM - blablabla',
                OperationType::Debit, ComptaModeReglement::PRELEVEMENT, ComptaEvenement::GESTION, ComptaCategorie::FRAIS_DE_COMPTE, 1, null, null],
            'Mailchimp' => ['CB MAILCHIMP FACT blablabla', OperationType::Debit,
                ComptaModeReglement::CB, ComptaEvenement::ASSOCIATION_AFUP, ComptaCategorie::MAILCHIMP, 1, null, null],
            'AWS' => ['CB AWS EMEA FACT blablabla', OperationType::Debit,
                ComptaModeReglement::CB, ComptaEvenement::ASSOCIATION_AFUP, ComptaCategorie::OUTILS, 1, null, null],
            'gandi.net' => ['CB GANDI FACT blablabla', OperationType::Debit,
                ComptaModeReglement::CB, ComptaEvenement::ASSOCIATION_AFUP, ComptaCategorie::GANDI, 1, null, null],
            'Twilio' => ['CB Twilio blablabla', OperationType::Debit,
                ComptaModeReglement::CB, ComptaEvenement::ASSOCIATION_AFUP, ComptaCategorie::OUTILS, 1, null, null],
        ];
    }

    #[DataProvider('qualifierData')]
    public function testQualifier(
        string $operationDescription,
        OperationType $operationType,
        int $expectedIdModeReglement,
        int $expectedEvenement,
        int $expectedCategorie,
        int $expectedAttachment,
        ?float $expectedHT,
        ?string $expectedHTKey,
    ): void {
        $operation = new Operation('2022-02-22', $operationDescription, 100, $operationType, '1234');
        $qualifier = new AutoQualifier($this->fakeRules());
        $actual = $qualifier->qualify($operation);

        self::assertEquals($expectedCategorie, $actual['categorie']);
        self::assertEquals($expectedEvenement, $actual['evenement']);
        self::assertEquals($expectedIdModeReglement, $actual['idModeReglement']);
        self::assertEquals($expectedAttachment, $actual['attachmentRequired']);

        if ($expectedHTKey) {
            self::assertEquals($expectedHT, $actual[$expectedHTKey]);
        }
    }

    /**
     * @return array<Rule>
     */
    private function fakeRules(): array
    {
        return [
            $this->createRule(1, 'VIR sprd.net', 'VIR SEPA sprd.net AG', true, ComptaModeReglement::VIREMENT, '0', ComptaCategorie::GOODIES, ComptaEvenement::ASSOCIATION_AFUP, true),
            $this->createRule(2, 'CB COM AFUP', '*CB COM AFUP ', false, ComptaModeReglement::PRELEVEMENT, '5_5', ComptaCategorie::FRAIS_DE_COMPTE, ComptaEvenement::GESTION, null),
            $this->createRule(3, 'COTIS ASSOCIATIS ESSENTIEL', '* COTIS ASSOCIATIS ESSENTIEL', false, ComptaModeReglement::PRELEVEMENT, '10', ComptaCategorie::FRAIS_DE_COMPTE, ComptaEvenement::GESTION, null),
            $this->createRule(4, 'URSSAF', 'PRLV URSSAF', false, ComptaModeReglement::PRELEVEMENT, '20', ComptaCategorie::CHARGES_SOCIALES, ComptaEvenement::GESTION, null),
            $this->createRule(5, 'DGFIP', 'PRLV B2B DGFIP', false, ComptaModeReglement::PRELEVEMENT, null, ComptaCategorie::PRELEVEMENT_SOURCE, ComptaEvenement::GESTION, null),
            $this->createRule(6, 'MALAKOFF HUMANIS', 'PRLV A3M - RETRAITE - MALAKOFF HUMANIS', false, ComptaModeReglement::PRELEVEMENT, null, ComptaCategorie::CHARGES_SOCIALES, ComptaEvenement::GESTION, null),
            $this->createRule(7, 'Online SAS', 'PRLV Online SAS -', false, ComptaModeReglement::PRELEVEMENT, null, ComptaCategorie::OUTILS, ComptaEvenement::ASSOCIATION_AFUP, true),
            $this->createRule(8, 'meetup.org', 'CB MEETUP ORG', false, ComptaModeReglement::CB, null, ComptaCategorie::MEETUP, ComptaEvenement::ASSOCIATION_AFUP, true),
            $this->createRule(9, 'POINT TRANSACTION SYSTEM', 'PRLV POINT TRANSACTION SYSTEM -', false, ComptaModeReglement::PRELEVEMENT, null, ComptaCategorie::FRAIS_DE_COMPTE, ComptaEvenement::GESTION, true),
            $this->createRule(10, 'Mailchimp', 'CB MAILCHIMP FACT', false, ComptaModeReglement::CB, null, ComptaCategorie::MAILCHIMP, ComptaEvenement::ASSOCIATION_AFUP, true),
            $this->createRule(11, 'AWS', 'CB AWS EMEA FACT', false, ComptaModeReglement::CB, null, ComptaCategorie::OUTILS, ComptaEvenement::ASSOCIATION_AFUP, true),
            $this->createRule(12, 'gandi.net', 'CB GANDI FACT', false, ComptaModeReglement::CB, null, ComptaCategorie::GANDI, ComptaEvenement::ASSOCIATION_AFUP, true),
            $this->createRule(13, 'Twilio', 'CB Twilio', false, ComptaModeReglement::CB, null, ComptaCategorie::OUTILS, ComptaEvenement::ASSOCIATION_AFUP, true),
        ];
    }

    public function testRuleWithCategoryButNoEvent(): void
    {
        $rule = $this->createRule(1, 'test', 'MATCH', null, null, null, ComptaCategorie::GOODIES, null, null);
        $qualifier = new AutoQualifier([$rule]);
        $actual = $qualifier->qualify(new Operation('2022-02-22', 'MATCH something', 100, OperationType::Credit, '1'));

        self::assertEquals(ComptaCategorie::GOODIES, $actual['categorie'], 'Category must be applied even when event is null');
        self::assertEquals(AutoQualifier::DEFAULT_EVENEMENT, $actual['evenement'], 'Event must stay at default when rule has no event');
    }

    public function testRuleWithEventButNoCategory(): void
    {
        $rule = $this->createRule(1, 'test', 'MATCH', null, null, null, null, ComptaEvenement::GESTION, null);
        $qualifier = new AutoQualifier([$rule]);
        $actual = $qualifier->qualify(new Operation('2022-02-22', 'MATCH something', 100, OperationType::Credit, '1'));

        self::assertEquals(ComptaEvenement::GESTION, $actual['evenement'], 'Event must be applied even when category is null');
        self::assertEquals(AutoQualifier::DEFAULT_CATEGORIE, $actual['categorie'], 'Category must stay at default when rule has no category');
    }

    public function testRuleWithNeitherCategoryNorEvent(): void
    {
        $rule = $this->createRule(1, 'test', 'MATCH', null, null, null, null, null, null);
        $qualifier = new AutoQualifier([$rule]);
        $actual = $qualifier->qualify(new Operation('2022-02-22', 'MATCH something', 100, OperationType::Credit, '1'));

        self::assertEquals(AutoQualifier::DEFAULT_CATEGORIE, $actual['categorie']);
        self::assertEquals(AutoQualifier::DEFAULT_EVENEMENT, $actual['evenement']);
    }

    private function createRule(
        int $id,
        string $label,
        string $condition,
        ?bool $isCredit,
        ?int $paymentTypeId,
        ?string $vat,
        ?int $categoryId,
        ?int $eventId,
        ?bool $attachmentRequired,
    ): Rule {
        $category = null;
        if ($categoryId !== null) {
            $category = new Category();
            $category->id = $categoryId;
        }

        $event = null;
        if ($eventId !== null) {
            $event = new Event();
            $event->id = $eventId;
        }

        $rule = new Rule();
        $rule->id = $id;
        $rule->label = $label;
        $rule->condition = $condition;
        $rule->isCredit = $isCredit;
        $rule->paymentTypeId = $paymentTypeId;
        $rule->vat = $vat;
        $rule->category = $category;
        $rule->event = $event;
        $rule->attachmentRequired = $attachmentRequired;

        return $rule;
    }
}
