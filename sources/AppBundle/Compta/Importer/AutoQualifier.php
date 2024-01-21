<?php

namespace AppBundle\Compta\Importer;

use AppBundle\Model\ComptaCategorie;
use AppBundle\Model\ComptaEvenement;
use AppBundle\Model\ComptaModeReglement;

class AutoQualifier
{
    const DEFAULT_CATEGORIE = 26; // "A déterminer"
    const DEFAULT_EVENEMENT = 8; // "A déterminer"
    const DEFAULT_REGLEMENT = 9;
    const DEFAULT_ATTACHMENT = 0;

    public static function qualify(Operation $operation)
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

        if ($operation->isCredit()) {
            if (0 === strpos($operationQualified['description'], 'VIR SEPA sprd.net AG')) {
                $operationQualified['evenement'] = ComptaEvenement::ASSOCIATION_AFUP;
                $operationQualified['categorie'] = ComptaCategorie::GOODIES;
                $operationQualified['attachmentRequired'] = 1;
            }
        } else {
            if (0 === strpos($operationQualified['description'], '*CB COM AFUP ')) {
                $operationQualified['idModeReglement'] = ComptaModeReglement::PRELEVEMENT;
                $operationQualified['evenement'] = ComptaEvenement::GESTION;
                $operationQualified['categorie'] = ComptaCategorie::FRAIS_DE_COMPTE;
            }

            if (0 === strpos($operationQualified['description'], '* COTIS ASSOCIATIS ESSENTIEL')) {
                $operationQualified['idModeReglement'] = ComptaModeReglement::PRELEVEMENT;
                $operationQualified['evenement'] = ComptaEvenement::GESTION;
                $operationQualified['categorie'] = ComptaCategorie::FRAIS_DE_COMPTE;
            }

            if (0 === strpos(strtoupper($operationQualified['description']), 'PRLV URSSAF')) {
                $operationQualified['evenement'] = ComptaEvenement::GESTION;
                $operationQualified['categorie'] = ComptaCategorie::CHARGES_SOCIALES;
            }

            if ($operationQualified['description'] === 'PRLV B2B DGFIP') {
                $operationQualified['evenement'] = ComptaEvenement::GESTION;
                $operationQualified['categorie'] = ComptaCategorie::PRELEVEMENT_SOURCE;
            }

            if (0 === strpos($operationQualified['description'], 'PRLV A3M - RETRAITE - MALAKOFF HUMANIS')) {
                $operationQualified['evenement'] = ComptaEvenement::GESTION;
                $operationQualified['categorie'] = ComptaCategorie::CHARGES_SOCIALES;
            }

            if (0 === strpos($operationQualified['description'], 'PRLV Online SAS -')) {
                $operationQualified['evenement'] = ComptaEvenement::ASSOCIATION_AFUP;
                $operationQualified['categorie'] = ComptaCategorie::OUTILS;
                $operationQualified['attachmentRequired'] = 1;
            }

            if (0 === strpos($operationQualified['description'], 'CB MEETUP ORG')) {
                $operationQualified['evenement'] = ComptaEvenement::ASSOCIATION_AFUP;
                $operationQualified['categorie'] = ComptaCategorie::MEETUP;
                $operationQualified['attachmentRequired'] = 1;
            }

            if (0 === strpos($operationQualified['description'], 'PRLV POINT TRANSACTION SYSTEM -')) {
                $operationQualified['evenement'] = ComptaEvenement::GESTION;
                $operationQualified['categorie'] = ComptaCategorie::FRAIS_DE_COMPTE;
                $operationQualified['attachmentRequired'] = 1;
            }

            if (0 === strpos(strtoupper($operationQualified['description']), 'CB MAILCHIMP FACT')) {
                $operationQualified['evenement'] = ComptaEvenement::ASSOCIATION_AFUP;
                $operationQualified['categorie'] = ComptaCategorie::MAILCHIMP;
                $operationQualified['attachmentRequired'] = 1;
            }

            if (0 === strpos($operationQualified['description'], 'CB AWS EMEA FACT')) {
                $operationQualified['evenement'] = ComptaEvenement::ASSOCIATION_AFUP;
                $operationQualified['categorie'] = ComptaCategorie::OUTILS;
                $operationQualified['attachmentRequired'] = 1;
            }

            if (0 === strpos($operationQualified['description'], 'CB GANDI FACT')) {
                $operationQualified['evenement'] = ComptaEvenement::ASSOCIATION_AFUP;
                $operationQualified['categorie'] = ComptaCategorie::GANDI;
                $operationQualified['attachmentRequired'] = 1;
            }

            if (0 === strpos($operationQualified['description'], 'CB Twilio')) {
                $operationQualified['evenement'] = ComptaEvenement::ASSOCIATION_AFUP;
                $operationQualified['categorie'] = ComptaCategorie::OUTILS;
                $operationQualified['attachmentRequired'] = 1;
            }
        }

        return $operationQualified;
    }
}
