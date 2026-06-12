<?php

declare(strict_types=1);

namespace AppBundle\AssembleeGenerale\Entity;

use AppBundle\Doctrine\Type\UnixTimestampType;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'afup_assemblee_generale')]
class AssembleeGenerale
{
    #[ORM\Id]
    #[ORM\Column(type: UnixTimestampType::NAME)]
    public \DateTime $date;

    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $description = null;
}
