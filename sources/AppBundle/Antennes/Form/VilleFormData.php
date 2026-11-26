<?php

declare(strict_types=1);

namespace AppBundle\Antennes\Form;

use AppBundle\Antennes\Point;
use AppBundle\Antennes\Position;
use AppBundle\Antennes\Ville;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Les champs sont nullables pour accepter un formulaire partiellement rempli :
 * la complétude est vérifiée par CarteFormData, qui sait si la ville est obligatoire.
 */
final class VilleFormData
{
    public ?int $premierPointX = null;
    public ?int $premierPointY = null;
    public ?int $deuxiemePointX = null;
    public ?int $deuxiemePointY = null;
    public ?int $troisiemePointX = null;
    public ?int $troisiemePointY = null;

    #[Assert\Range(min: -90, max: 90)]
    public ?float $latitude = null;

    #[Assert\Range(min: -180, max: 180)]
    public ?float $longitude = null;

    public static function fromVille(Ville $ville): self
    {
        $data = new self();
        $data->premierPointX = $ville->premierPoint->x;
        $data->premierPointY = $ville->premierPoint->y;
        $data->deuxiemePointX = $ville->deuxiemePoint->x;
        $data->deuxiemePointY = $ville->deuxiemePoint->y;
        $data->troisiemePointX = $ville->troisiemePoint->x;
        $data->troisiemePointY = $ville->troisiemePoint->y;
        $data->latitude = $ville->position->latitude;
        $data->longitude = $ville->position->longitude;

        return $data;
    }

    public function isEmpty(): bool
    {
        return array_filter($this->values(), fn(int|float|null $valeur): bool => $valeur !== null) === [];
    }

    public function isComplete(): bool
    {
        return !\in_array(null, $this->values(), true);
    }

    public function toVille(): Ville
    {
        if (!$this->isComplete()) {
            throw new \LogicException('La ville doit être complète pour être convertie.');
        }

        return new Ville(
            new Point((int) $this->premierPointX, (int) $this->premierPointY),
            new Point((int) $this->deuxiemePointX, (int) $this->deuxiemePointY),
            new Point((int) $this->troisiemePointX, (int) $this->troisiemePointY),
            new Position((float) $this->latitude, (float) $this->longitude),
        );
    }

    /**
     * @return list<int|float|null>
     */
    private function values(): array
    {
        return [
            $this->premierPointX,
            $this->premierPointY,
            $this->deuxiemePointX,
            $this->deuxiemePointY,
            $this->troisiemePointX,
            $this->troisiemePointY,
            $this->latitude,
            $this->longitude,
        ];
    }
}
