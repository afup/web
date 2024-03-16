<?php

use AppBundle\Model\ComptaCategorie;
use AppBundle\Model\ComptaEvenement;
use AppBundle\Model\ComptaModeReglement;
use Phinx\Seed\AbstractSeed;

class ComptaRegle extends AbstractSeed
{
    public function run()
    {
        $data = [
            [
                'id' => 1,
                'label' => 'VIR sprd.net',
                'condition' => 'VIR SEPA sprd.net AG',
                'is_credit' => '1',
                'mode_regl_id' => ComptaModeReglement::VIREMENT,
                'vat' => null,
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
                'vat' => null,
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
                'vat' => null,
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
                'vat' => null,
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

        $table = $this->table('compta_regle');
        $table->truncate();

        $table
            ->insert($data)
            ->save();
    }
}
