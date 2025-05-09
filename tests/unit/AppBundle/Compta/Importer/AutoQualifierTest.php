<?php

declare(strict_types=1);

namespace AppBundle\Tests\Compta\Importer;

use AppBundle\Compta\Importer\AutoQualifier;
use AppBundle\Compta\Importer\Operation;
use AppBundle\Model\ComptaCategorie;
use AppBundle\Model\ComptaEvenement;
use AppBundle\Model\ComptaModeReglement;
use PHPUnit\Framework\TestCase;

final class AutoQualifierTest extends TestCase
{
    public function testDefaultOperation(): void
    {
        $operation = new Operation('2022-02-22', 'DESCRIPTION', '123', Operation::CREDIT, '1234');
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

    public function idModeReglementData(): array
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

    /**
     * @dataProvider idModeReglementData
     */
    public function testIdModeReglement($description, $idModeReglement): void
    {
        $operation = new Operation('2022-02-22', $description, '123', Operation::CREDIT, '1234');
        $qualifier = new AutoQualifier([]);
        $actual = $qualifier->qualify($operation);

        self::assertEquals($idModeReglement, $actual['idModeReglement']);
    }


    public function qualifierData(): array
    {
        return [
            'sprd.net' => ['VIR SEPA sprd.net AG blablabla', Operation::CREDIT,
                ComptaModeReglement::VIREMENT, ComptaEvenement::ASSOCIATION_AFUP, ComptaCategorie::GOODIES, 1, 100, 'montant_ht_soumis_tva_0'],
            'COM AFUP' => ['*CB COM AFUP blablabla', Operation::DEBIT,
                ComptaModeReglement::PRELEVEMENT, ComptaEvenement::GESTION, ComptaCategorie::FRAIS_DE_COMPTE, AutoQualifier::DEFAULT_ATTACHMENT, 94.79, 'montant_ht_soumis_tva_5_5'],
            'COTIS ASSOCIATIS' => ['* COTIS ASSOCIATIS ESSENTIEL blablabla', Operation::DEBIT,
                ComptaModeReglement::PRELEVEMENT, ComptaEvenement::GESTION, ComptaCategorie::FRAIS_DE_COMPTE, AutoQualifier::DEFAULT_ATTACHMENT, 90.91, 'montant_ht_soumis_tva_10'],
            'URSSAF' => ['PRLV URSSAF blablabla', Operation::DEBIT,
                ComptaModeReglement::PRELEVEMENT, ComptaEvenement::GESTION, ComptaCategorie::CHARGES_SOCIALES, AutoQualifier::DEFAULT_ATTACHMENT, 83.33, 'montant_ht_soumis_tva_20'],
            'DGFIP' => ['PRLV B2B DGFIP', Operation::DEBIT,
                ComptaModeReglement::PRELEVEMENT, ComptaEvenement::GESTION, ComptaCategorie::PRELEVEMENT_SOURCE, AutoQualifier::DEFAULT_ATTACHMENT, null, null],
            'RETRAITE' => ['PRLV A3M - RETRAITE - MALAKOFF HUMANIS blablabla', Operation::DEBIT,
                ComptaModeReglement::PRELEVEMENT, ComptaEvenement::GESTION, ComptaCategorie::CHARGES_SOCIALES, AutoQualifier::DEFAULT_ATTACHMENT, null, null],
            'Online.net' => ['PRLV Online SAS - blablabla', Operation::DEBIT,
                ComptaModeReglement::PRELEVEMENT, ComptaEvenement::ASSOCIATION_AFUP, ComptaCategorie::OUTILS, 1, null, null],
            'meetup.org' => ['CB MEETUP ORG blablabla', Operation::DEBIT,
                ComptaModeReglement::CB, ComptaEvenement::ASSOCIATION_AFUP, ComptaCategorie::MEETUP, 1, null, null],
            'POINT TRANSACTION' => ['PRLV POINT TRANSACTION SYSTEM - blablabla',
                Operation::DEBIT, ComptaModeReglement::PRELEVEMENT, ComptaEvenement::GESTION, ComptaCategorie::FRAIS_DE_COMPTE, 1, null, null],
            'Mailchimp' => ['CB MAILCHIMP FACT blablabla', Operation::DEBIT,
                ComptaModeReglement::CB, ComptaEvenement::ASSOCIATION_AFUP, ComptaCategorie::MAILCHIMP, 1, null, null],
            'AWS' => ['CB AWS EMEA FACT blablabla', Operation::DEBIT,
                ComptaModeReglement::CB, ComptaEvenement::ASSOCIATION_AFUP, ComptaCategorie::OUTILS, 1, null, null],
            'gandi.net' => ['CB GANDI FACT blablabla', Operation::DEBIT,
                ComptaModeReglement::CB, ComptaEvenement::ASSOCIATION_AFUP, ComptaCategorie::GANDI, 1, null, null],
            'Twilio' => ['CB Twilio blablabla', Operation::DEBIT,
                ComptaModeReglement::CB, ComptaEvenement::ASSOCIATION_AFUP, ComptaCategorie::OUTILS, 1, null, null],
        ];
    }

    /**
     * @dataProvider qualifierData
     */
    public function testQualifier(
        string $operationDescription,
        string $operationType,
        int $expectedIdModeReglement,
        int $expectedEvenement,
        int $expectedCategorie,
        int $expectedAttachment,
        ?float $expectedHT,
        ?string $expectedHTKey,
    ): void {
        $operation = new Operation('2022-02-22', $operationDescription, '100', $operationType, '1234');
        $qualifier = new AutoQualifier($this->fakeBD());
        $actual = $qualifier->qualify($operation);

        self::assertEquals($expectedCategorie, $actual['categorie']);
        self::assertEquals($expectedEvenement, $actual['evenement']);
        self::assertEquals($expectedIdModeReglement, $actual['idModeReglement']);
        self::assertEquals($expectedAttachment, $actual['attachmentRequired']);

        if ($expectedHTKey) {
            self::assertEquals($expectedHT, $actual[$expectedHTKey]);
        }
    }

    private function fakeBD(): array
    {
        return [
            [
                'id' => 1,
                'label' => 'VIR sprd.net',
                'condition' => 'VIR SEPA sprd.net AG',
                'is_credit' => '1',
                'mode_regl_id' => ComptaModeReglement::VIREMENT,
                'vat' => '0',
                'category_id' => ComptaCategorie::GOODIES,
                'event_id' => ComptaEvenement::ASSOCIATION_AFUP,
                'attachment_required' => 1,
            ],
            [
                'id' => 2,
                'label' => 'CB COM AFUP',
                'condition' => '*CB COM AFUP ',
                'is_credit' => 0,
                'mode_regl_id' => ComptaModeReglement::PRELEVEMENT,
                'vat' => '5_5',
                'category_id' => ComptaCategorie::FRAIS_DE_COMPTE,
                'event_id' => ComptaEvenement::GESTION,
                'attachment_required' => null,
            ],
            [
                'id' => 3,
                'label' => 'COTIS ASSOCIATIS ESSENTIEL',
                'condition' => '* COTIS ASSOCIATIS ESSENTIEL',
                'is_credit' => 0,
                'mode_regl_id' => ComptaModeReglement::PRELEVEMENT,
                'vat' => '10',
                'category_id' => ComptaCategorie::FRAIS_DE_COMPTE,
                'event_id' => ComptaEvenement::GESTION,
                'attachment_required' => null,
            ],
            [
                'id' => 4,
                'label' => 'URSSAF',
                'condition' => 'PRLV URSSAF',
                'is_credit' => 0,
                'mode_regl_id' => ComptaModeReglement::PRELEVEMENT,
                'vat' => '20',
                'category_id' => ComptaCategorie::CHARGES_SOCIALES,
                'event_id' => ComptaEvenement::GESTION,
                'attachment_required' => null,
            ],
            [
                'id' => 5,
                'label' => 'DGFIP',
                'condition' => 'PRLV B2B DGFIP',
                'is_credit' => 0,
                'mode_regl_id' => ComptaModeReglement::PRELEVEMENT,
                'vat' => null,
                'category_id' => ComptaCategorie::PRELEVEMENT_SOURCE,
                'event_id' => ComptaEvenement::GESTION,
                'attachment_required' => null,
            ],
            [
                'id' => 6,
                'label' => 'MALAKOFF HUMANIS',
                'condition' => 'PRLV A3M - RETRAITE - MALAKOFF HUMANIS',
                'is_credit' => 0,
                'mode_regl_id' => ComptaModeReglement::PRELEVEMENT,
                'vat' => null,
                'category_id' => ComptaCategorie::CHARGES_SOCIALES,
                'event_id' => ComptaEvenement::GESTION,
                'attachment_required' => null,
            ],
            [
                'id' => 7,
                'label' => 'Online SAS',
                'condition' => 'PRLV Online SAS -',
                'is_credit' => 0,
                'mode_regl_id' => ComptaModeReglement::PRELEVEMENT,
                'vat' => null,
                'category_id' => ComptaCategorie::OUTILS,
                'event_id' => ComptaEvenement::ASSOCIATION_AFUP,
                'attachment_required' => 1,
            ],
            [
                'id' => 8,
                'label' => 'meetup.org',
                'condition' => 'CB MEETUP ORG',
                'is_credit' => 0,
                'mode_regl_id' => ComptaModeReglement::CB,
                'vat' => null,
                'category_id' => ComptaCategorie::MEETUP,
                'event_id' => ComptaEvenement::ASSOCIATION_AFUP,
                'attachment_required' => 1,
            ],
            [
                'id' => 9,
                'label' => 'POINT TRANSACTION SYSTEM',
                'condition' => 'PRLV POINT TRANSACTION SYSTEM -',
                'is_credit' => 0,
                'mode_regl_id' => ComptaModeReglement::PRELEVEMENT,
                'vat' => null,
                'category_id' => ComptaCategorie::FRAIS_DE_COMPTE,
                'event_id' => ComptaEvenement::GESTION,
                'attachment_required' => 1,
            ],
            [
                'id' => 10,
                'label' => 'Mailchimp',
                'condition' => 'CB MAILCHIMP FACT',
                'is_credit' => 0,
                'mode_regl_id' => ComptaModeReglement::CB,
                'vat' => null,
                'category_id' => ComptaCategorie::MAILCHIMP,
                'event_id' => ComptaEvenement::ASSOCIATION_AFUP,
                'attachment_required' => 1,
            ],
            [
                'id' => 11,
                'label' => 'AWS',
                'condition' => 'CB AWS EMEA FACT',
                'is_credit' => 0,
                'mode_regl_id' => ComptaModeReglement::CB,
                'vat' => null,
                'category_id' => ComptaCategorie::OUTILS,
                'event_id' => ComptaEvenement::ASSOCIATION_AFUP,
                'attachment_required' => 1,
            ],
            [
                'id' => 12,
                'label' => 'gandi.net',
                'condition' => 'CB GANDI FACT',
                'is_credit' => 0,
                'mode_regl_id' => ComptaModeReglement::CB,
                'vat' => null,
                'category_id' => ComptaCategorie::GANDI,
                'event_id' => ComptaEvenement::ASSOCIATION_AFUP,
                'attachment_required' => 1,
            ],
            [
                'id' => 13,
                'label' => 'Twilio',
                'condition' => 'CB Twilio',
                'is_credit' => 0,
                'mode_regl_id' => ComptaModeReglement::CB,
                'vat' => null,
                'category_id' => ComptaCategorie::OUTILS,
                'event_id' => ComptaEvenement::ASSOCIATION_AFUP,
                'attachment_required' => 1,
            ],
        ];
    }
}
