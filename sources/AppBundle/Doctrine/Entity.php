<?php

declare(strict_types=1);

namespace AppBundle\Doctrine;

use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
abstract class Entity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    public function isPersisted(): bool
    {
        return isset($this->id);
    }
}
