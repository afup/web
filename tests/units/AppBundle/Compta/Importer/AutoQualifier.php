<?php

namespace AppBundle\Compta\Importer\test\units;

use AppBundle\Compta\Importer\Operation;
use AppBundle\Compta\Importer\AutoQualifier as TestedClass;
use AppBundle\Model\ComptaCategorie;
use AppBundle\Model\ComptaEvenement;
use AppBundle\Model\ComptaModeReglement;

class AutoQualifier extends \atoum
{
    public function testDefaultOperation()
    {
        $operation = new Operation('2022-02-22', 'DESCRIPTION', '123', Operation::CREDIT, '1234');
        $actual = TestedClass::qualify($operation);

        $this->string($actual['date_ecriture'])->isEqualTo('2022-02-22');
        $this->integer($actual['idoperation'])->isEqualTo(2);
        $this->string($actual['montant'])->isEqualTo('123');
        $this->integer($actual['categorie'])->isEqualTo(TestedClass::DEFAULT_CATEGORIE);
        $this->integer($actual['evenement'])->isEqualTo(TestedClass::DEFAULT_EVENEMENT);
        $this->integer($actual['idModeReglement'])->isEqualTo(TestedClass::DEFAULT_REGLEMENT);
        $this->integer($actual['attachmentRequired'])->isEqualTo(TestedClass::DEFAULT_ATTACHMENT);
        $this->string($actual['description'])->isEqualTo('DESCRIPTION');
    }

    public function idModeReglementData(): array
    {
        return [
            'Défaut' => ['XXX blablabla', TestedClass::DEFAULT_REGLEMENT],
            'Autre' => ['Blablabla CHE', TestedClass::DEFAULT_REGLEMENT],
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
    public function testIdModeReglement($description, $idModeReglement)
    {
        $operation = new Operation('2022-02-22', $description, '123', Operation::CREDIT, '1234');
        $actual = TestedClass::qualify($operation);

        $this->integer($actual['idModeReglement'])->isEqualTo($idModeReglement);
    }


    public function qualifierData(): array
    {
        return [
            'sprd.net' => ['VIR SEPA sprd.net AG blablabla', Operation::CREDIT,
                ComptaModeReglement::VIREMENT, ComptaEvenement::ASSOCIATION_AFUP, ComptaCategorie::GOODIES, 1],
            'COM AFUP' => ['*CB COM AFUP blablabla', Operation::DEBIT,
                ComptaModeReglement::PRELEVEMENT, ComptaEvenement::GESTION, ComptaCategorie::FRAIS_DE_COMPTE, TestedClass::DEFAULT_ATTACHMENT],
            'COTIS ASSOCIATIS' => ['* COTIS ASSOCIATIS ESSENTIEL blablabla', Operation::DEBIT,
                ComptaModeReglement::PRELEVEMENT, ComptaEvenement::GESTION, ComptaCategorie::FRAIS_DE_COMPTE, TestedClass::DEFAULT_ATTACHMENT],
            'URSSAF' => ['PRLV URSSAF blablabla', Operation::DEBIT,
                ComptaModeReglement::PRELEVEMENT, ComptaEvenement::GESTION, ComptaCategorie::CHARGES_SOCIALES, TestedClass::DEFAULT_ATTACHMENT],
            'DGFIP' => ['PRLV B2B DGFIP', Operation::DEBIT,
                ComptaModeReglement::PRELEVEMENT, ComptaEvenement::GESTION, ComptaCategorie::PRELEVEMENT_SOURCE, TestedClass::DEFAULT_ATTACHMENT],
            'RETRAITE' => ['PRLV A3M - RETRAITE - MALAKOFF HUMANIS blablabla', Operation::DEBIT,
                ComptaModeReglement::PRELEVEMENT, ComptaEvenement::GESTION, ComptaCategorie::CHARGES_SOCIALES, TestedClass::DEFAULT_ATTACHMENT],
            'Online.net' => ['PRLV Online SAS - blablabla', Operation::DEBIT,
                ComptaModeReglement::PRELEVEMENT, ComptaEvenement::ASSOCIATION_AFUP, ComptaCategorie::OUTILS, 1],
            'meetup.org' => ['CB MEETUP ORG blablabla', Operation::DEBIT,
                ComptaModeReglement::CB, ComptaEvenement::ASSOCIATION_AFUP, ComptaCategorie::MEETUP, 1],
            'POINT TRANSACTION' => ['PRLV POINT TRANSACTION SYSTEM - blablabla',
                Operation::DEBIT, ComptaModeReglement::PRELEVEMENT, ComptaEvenement::GESTION, ComptaCategorie::FRAIS_DE_COMPTE, 1],
            'Mailchimp' => ['CB MAILCHIMP FACT blablabla', Operation::DEBIT,
                ComptaModeReglement::CB, ComptaEvenement::ASSOCIATION_AFUP, ComptaCategorie::MAILCHIMP, 1],
            'AWS' => ['CB AWS EMEA FACT blablabla', Operation::DEBIT,
                ComptaModeReglement::CB, ComptaEvenement::ASSOCIATION_AFUP, ComptaCategorie::OUTILS, 1],
            'gandi.net' => ['CB GANDI FACT blablabla', Operation::DEBIT,
                ComptaModeReglement::CB, ComptaEvenement::ASSOCIATION_AFUP, ComptaCategorie::GANDI, 1],
            'Twilio' => ['CB Twilio blablabla', Operation::DEBIT,
                ComptaModeReglement::CB, ComptaEvenement::ASSOCIATION_AFUP, ComptaCategorie::OUTILS, 1],
        ];
    }

    /**
     * @dataProvider qualifierData
     */
    public function testQualifier($operationDescription, $operationType,
        $expectedIdModeReglement, $expectedEvenement, $expectedCategorie, $expectedAttachment)
    {
        $operation = new Operation('2022-02-22', $operationDescription, '123', $operationType, '1234');
        $actual = TestedClass::qualify($operation);

        $this->integer($actual['categorie'])->isEqualTo($expectedCategorie);
        $this->integer($actual['evenement'])->isEqualTo($expectedEvenement);
        $this->integer($actual['idModeReglement'])->isEqualTo($expectedIdModeReglement);
        $this->integer($actual['attachmentRequired'])->isEqualTo($expectedAttachment);
    }
}
