<?php

declare(strict_types=1);

namespace AppBundle\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Type;

class UnixTimestampType extends Type
{
    public const NAME = 'unix_timestamp';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getIntegerTypeDeclarationSQL($column);
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?\DateTime
    {
        if ($value === null) {
            return null;
        }

        $date = new \DateTime();
        $date->setTimestamp((int) $value);

        return $date;
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?int
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->getTimestamp();
        }

        throw InvalidType::new($value, static::class, ['null', \DateTimeInterface::class]);
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
