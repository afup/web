<?php

declare(strict_types=1);

namespace Afup\Tests\Stubs\PHPStan\Doctrine;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class EntityWithNullableIdAndDefaultValue
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $a = null;
}

#[ORM\Entity]
class EntityWithNullableIdWithoutDefaultValue
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $b;
}

#[ORM\Entity]
class EntityWithNullableIdAndDefaultValue0
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $c = 0;
}

#[ORM\Entity]
class EntityWithNullableIdAndDefaultValueNumber
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $d = 123;
}
