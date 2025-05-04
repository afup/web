<?php

declare(strict_types=1);

namespace AppBundle\Compta\Importer;

use Afup\Site\Utils\Vat;
use AppBundle\Model\ComptaModeReglement;

class AutoQualifier
{
    const DEFAULT_CATEGORIE = 26; // "A déterminer"
    const DEFAULT_EVENEMENT = 8; // "A déterminer"
    const DEFAULT_REGLEMENT = 9;
    const DEFAULT_ATTACHMENT = 0;

    public function __construct(protected array $rules)
    {
    }

    public function qualify(Operation $operation): array
    {
        $operationQualified = [];

        $operationQualified['date_ecriture'] = $operation->getDateEcriture();
        $operationQualified['description'] = $operation->getDescription();
        $operationQualified['idoperation'] = $operation->isCredit() ? 2 : 1;
        $operationQualified['montant'] = $operation->getMontant();

        $operationQualified['categorie'] = self::DEFAULT_CATEGORIE;
        $operationQualified['evenement'] = self::DEFAULT_EVENEMENT;
        $operationQualified['idModeReglement'] = self::DEFAULT_REGLEMENT;
        $operationQualified['attachmentRequired'] = self::DEFAULT_ATTACHMENT;

        $firstPartDescription = strtoupper(explode(' ', $operationQualified['description'])[0]);
        switch ($firstPartDescription) {
            case 'CB':
                $operationQualified['idModeReglement'] = ComptaModeReglement::CB;
                break;
            case 'VIR':
                $operationQualified['idModeReglement'] = ComptaModeReglement::VIREMENT;
                break;
            case 'CHE':
            case 'REM':
                $operationQualified['idModeReglement'] = ComptaModeReglement::CHEQUE;
                break;
            case 'PRLV':
                $operationQualified['idModeReglement'] = ComptaModeReglement::PRELEVEMENT;
                break;
        }

        // init VAT
        $operationQualified['montant_ht_soumis_tva_0'] = null;
        $operationQualified['montant_ht_soumis_tva_5_5'] = null;
        $operationQualified['montant_ht_soumis_tva_10'] = null;
        $operationQualified['montant_ht_soumis_tva_20'] = null;

        foreach ($this->rules as $rule) {
            if (($operation->isCredit() === (bool) $rule['is_credit'] || is_null($rule['is_credit'])) && str_contains($operationQualified['description'], (string) $rule['condition'])) {
                if (null !== $rule['event_id']) {
                    $operationQualified['evenement'] = $rule['event_id'];
                }
                if (null !== $rule['category_id']) {
                    $operationQualified['categorie'] = $rule['category_id'];
                }
                if (null !== $rule['attachment_required']) {
                    $operationQualified['attachmentRequired'] = $rule['attachment_required'];
                }
                if (null !== $rule['mode_regl_id']) {
                    $operationQualified['idModeReglement'] = $rule['mode_regl_id'];
                }
                if (null !== $rule['vat']) {
                    $tx = ['0' => 0, '5_5' => 0.055, '10' => 0.1, '20' => 0.2];
                    $operationQualified['montant_ht_soumis_tva_' . $rule['vat']] = Vat::getRoundedWithoutVatPriceFromPriceWithVat($operationQualified['montant'], $tx[$rule['vat']]);
                }
                break;
            }
        }

        return $operationQualified;
    }
}
