<?php

declare(strict_types=1);

namespace AppBundle\Antennes\Form;

use AppBundle\Antennes\AncrageLegende;
use AppBundle\Antennes\Carte;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class CarteFormData
{
    public bool $afficherSurCarte = false;
    public bool $utiliserSecondeCouleur = false;
    public ?AncrageLegende $ancrageLegende = AncrageLegende::Droite;

    #[Assert\Valid]
    public ?VilleFormData $premiereVille;

    #[Assert\Valid]
    public ?VilleFormData $secondeVille = null;

    public function __construct()
    {
        $this->premiereVille = new VilleFormData();
    }

    public static function fromCarte(?Carte $carte): self
    {
        $data = new self();

        if (!$carte instanceof Carte) {
            return $data;
        }

        $data->afficherSurCarte = true;
        $data->utiliserSecondeCouleur = $carte->utiliserSecondeCouleur;
        $data->ancrageLegende = $carte->ancrageLegende;
        $data->premiereVille = VilleFormData::fromVille($carte->premiereVille);
        $data->secondeVille = $carte->secondeVille !== null ? VilleFormData::fromVille($carte->secondeVille) : null;

        return $data;
    }

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context): void
    {
        if (!$this->afficherSurCarte) {
            return;
        }

        if (!$this->ancrageLegende instanceof AncrageLegende) {
            $context->buildViolation("L'ancrage de la légende est obligatoire pour afficher l'antenne sur la carte.")
                ->atPath('ancrageLegende')
                ->addViolation();
        }

        if (!$this->premiereVille instanceof VilleFormData || !$this->premiereVille->estComplete()) {
            $context->buildViolation("Tous les champs de la première ville sont obligatoires pour afficher l'antenne sur la carte.")
                ->atPath('premiereVille')
                ->addViolation();
        }

        if ($this->secondeVille instanceof VilleFormData && !$this->secondeVille->estVide() && !$this->secondeVille->estComplete()) {
            $context->buildViolation('La seconde ville doit être soit entièrement remplie, soit vide.')
                ->atPath('secondeVille')
                ->addViolation();
        }
    }

    public function toCarte(): ?Carte
    {
        if (!$this->afficherSurCarte
            || !$this->ancrageLegende instanceof AncrageLegende
            || !$this->premiereVille instanceof VilleFormData) {
            return null;
        }

        $secondeVille = $this->secondeVille instanceof VilleFormData && !$this->secondeVille->estVide()
            ? $this->secondeVille->toVille()
            : null;

        return new Carte(
            $this->utiliserSecondeCouleur,
            $this->ancrageLegende,
            $this->premiereVille->toVille(),
            $secondeVille,
        );
    }
}
