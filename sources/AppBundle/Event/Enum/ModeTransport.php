<?php

declare(strict_types=1);

namespace AppBundle\Event\Enum;

enum ModeTransport: int
{
    /** Valeur historique enregistrée quand le champ était laissé vide : jamais proposée dans les formulaires */
    case NonRenseigne = 0;
    case SeulThermique = 10;
    case SeulElectrique = 20;
    case SeulHybride = 30;
    case PassagersThermique = 40;
    case PassagersElectrique = 50;
    case PassagersHybride = 60;
    case Bus = 70;
    case Train = 80;
    case AvionEco = 90;
    case AvionBusiness = 100;
    case Commun = 110;

    /**
     * Modes proposés dans les formulaires, triés par libellé.
     *
     * @return list<self>
     */
    public static function choixParLibelle(): array
    {
        $cases = array_values(array_filter(self::cases(), static fn(self $mode): bool => $mode !== self::NonRenseigne));
        usort($cases, static fn(self $a, self $b): int => strcmp($a->label(), $b->label()));

        return $cases;
    }

    public function label(): string
    {
        return match ($this) {
            self::NonRenseigne => 'Non renseigné',
            self::SeulThermique => 'Voiture seul·e',
            self::SeulElectrique => 'Voiture seul·e électrique',
            self::SeulHybride => 'Voiture seul·e hybride',
            self::PassagersThermique => 'Voiture avec passagers·ères',
            self::PassagersElectrique => 'Voiture avec passagers·ères électrique',
            self::PassagersHybride => 'Voiture avec passagers·ères hybride',
            self::Bus => 'Bus',
            self::Train => 'Train',
            self::AvionEco => 'Avion classe économique',
            self::AvionBusiness => 'Avion classe business',
            self::Commun => 'Transports en commun',
        };
    }
}
