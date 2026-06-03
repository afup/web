<?php

declare(strict_types=1);

namespace AppBundle\AssembleeGenerale\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'afup_assemblee_generale')]
class AssembleeGenerale
{
    /**
     * Identifiant = timestamp Unix (colonne `date`, int unsigned, clé de fait de la table).
     *
     * On le mappe en `int` et non en `\DateTime` (via UnixTimestampType) car Doctrine
     * ne sait pas hasher un objet `DateTime` dans son identity map :
     * UnitOfWork::getIdHashByIdentifier() fait un implode() qui exige une valeur scalaire.
     * Un identifiant `\DateTime` provoque donc « Object of class DateTime could not be
     * converted to string » à chaque flush où l'entité est managée.
     */
    #[ORM\Id]
    #[ORM\Column]
    public int $date;

    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $description = null;
}
