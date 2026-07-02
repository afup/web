<?php

declare(strict_types=1);

namespace Afup\Tests\Stubs\PHPStan\Doctrine;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class EntityWithNonNullableId
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;
}

#[ORM\Entity]
class EntityWithoutGeneratedValue
{
    #[ORM\Id]
    #[ORM\Column]
    public ?int $id = null;
}

#[ORM\Entity]
class EntityWithStringId
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?string $id = null;
}

class NotAnEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;
}
