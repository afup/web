<?php

declare(strict_types=1);

namespace AppBundle\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Exception\ValueNotConvertible;
use Doctrine\DBAL\Types\Type;

/**
 * Type de base pour stocker un objet valeur dans une colonne JSON et l'hydrater
 * directement en objet PHP (plutôt qu'en tableau comme le type `json` natif).
 *
 * @template T of object
 */
abstract class JsonValueObjectType extends Type
{
    private const int ENCODE_FLAGS = JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION | JSON_FORCE_OBJECT;

    /**
     * @return class-string<T>
     */
    abstract protected function valueClass(): string;

    /**
     * @param array<mixed> $data
     *
     * @return T
     *
     * @throws \UnexpectedValueException si les données ne respectent pas la structure attendue
     */
    abstract protected function fromArray(array $data): object;

    /**
     * @param T $value
     *
     * @return array<string, mixed>
     */
    abstract protected function toArray(object $value): array;

    abstract public function getName(): string;

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getJsonTypeDeclarationSQL($column);
    }

    /**
     * @return T|null
     */
    #[\Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?object
    {
        if ($value === null) {
            return null;
        }

        if (!is_string($value)) {
            throw ValueNotConvertible::new($value, $this->getName());
        }

        try {
            $data = json_decode($value, true, 512, JSON_THROW_ON_ERROR);

            if (!is_array($data)) {
                throw new \UnexpectedValueException('un objet JSON est attendu');
            }

            return $this->fromArray($data);
        } catch (\JsonException|\UnexpectedValueException $e) {
            throw ValueNotConvertible::new($value, $this->getName(), $e->getMessage(), $e);
        }
    }

    #[\Override]
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        $class = $this->valueClass();
        if (!$value instanceof $class) {
            throw InvalidType::new($value, static::class, ['null', $class]);
        }

        return json_encode($this->toArray($value), self::ENCODE_FLAGS);
    }

    /**
     * @param array<mixed> $data
     */
    protected static function nullableString(array $data, string $key): ?string
    {
        $value = $data[$key] ?? null;

        if ($value !== null && !is_string($value)) {
            throw new \UnexpectedValueException(sprintf('la clé "%s" doit être une chaîne ou null', $key));
        }

        return $value;
    }

    /**
     * @param array<mixed> $data
     *
     * @return array<mixed>
     */
    protected static function arrayValue(array $data, string $key): array
    {
        $value = $data[$key] ?? null;

        if (!is_array($value)) {
            throw new \UnexpectedValueException(sprintf('la clé "%s" doit être un objet', $key));
        }

        return $value;
    }
}
