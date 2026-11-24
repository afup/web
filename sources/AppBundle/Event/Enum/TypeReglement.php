<?php

declare(strict_types=1);

namespace AppBundle\Event\Enum;

/**
 * Type de règlement d'une facture d'évènement (colonne afup_facturation_forum.type_reglement).
 */
enum TypeReglement: int
{
    case CarteBancaire = 0;
    case Cheque = 1;
    case Virement = 2;
    case Aucun = 3;
}
