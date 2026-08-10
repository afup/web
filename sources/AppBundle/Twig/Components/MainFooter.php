<?php

declare(strict_types=1);

namespace AppBundle\Twig\Components;

use Afup\Site\Corporate\Feuille;
use AppBundle\Site\Entity\Repository\FeuilleRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final readonly class MainFooter
{
    public function __construct(private FeuilleRepository $feuilleRepository) {}

    /**
     * @return array<array{nom: string, items: array<\AppBundle\Site\Entity\Feuille>}>
     */
    public function getColumns(): array
    {
        $footerColumns = [];
        foreach ($this->feuilleRepository->getFeuillesEnfant(Feuille::ID_FEUILLE_FOOTER) as $feuilleColonne) {
            if ($feuilleColonne->nom === null || $feuilleColonne->id === null) {
                continue;
            }

            $footerColumns[] = [
                'nom' => $feuilleColonne->nom,
                'items' => $this->feuilleRepository->getFeuillesEnfant($feuilleColonne->id),
            ];
        }

        return $footerColumns;
    }
}
