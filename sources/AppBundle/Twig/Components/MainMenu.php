<?php

declare(strict_types=1);

namespace AppBundle\Twig\Components;

use Afup\Site\Corporate\Feuille;
use AppBundle\Security\Authentication;
use AppBundle\Site\Entity\Feuille as FeuilleEntity;
use AppBundle\Site\Entity\Repository\FeuilleRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final readonly class MainMenu
{
    public function __construct(
        private RequestStack $requestStack,
        private FeuilleRepository $feuilleRepository,
        private Authentication $authentication,
    ) {}

    /**
     * @return array<array{isCurrent: boolean, lien: string, nom: string}>
     */
    public function getFeuilles(): array
    {
        $feuillesEnfants = $this->feuilleRepository->getFeuillesEnfant(Feuille::ID_FEUILLE_HEADER);

        if ($this->authentication->getAfupUserOrNull() instanceof UserInterface) {
            $feuilleLogin = new FeuilleEntity();
            $feuilleLogin->idParent = Feuille::ID_FEUILLE_HEADER;
            $feuilleLogin->lien = '/member';
            $feuilleLogin->alt = '';
            $feuilleLogin->position = 999;
            $feuilleLogin->etat = 1;
            $feuilleLogin->id = PHP_INT_MAX;
            $feuilleLogin->nom = 'Espace membre';
            $feuilleLogin->patterns = "#/admin/company#";

            $feuillesEnfants[] = $feuilleLogin;
        }

        $currentUri = $this->requestStack->getCurrentRequest()?->getRequestUri() ?? '';
        $feuilles = [];

        foreach ($feuillesEnfants as $feuille) {
            if ($feuille->lien === null || $feuille->nom === null || $feuille->id === null) {
                continue;
            }

            $isCurrent = false;
            if ($feuille->patterns) {
                foreach (explode(PHP_EOL, (string) $feuille->patterns) as $pattern) {
                    $pattern = trim($pattern);
                    if ($pattern === '') {
                        continue;
                    }

                    if (preg_match($pattern, $currentUri)) {
                        $isCurrent = true;
                    }
                }
            }

            if (str_contains($currentUri, (string) $feuille->lien)) {
                $isCurrent = true;
            }

            if (false === $isCurrent) {
                $enfants = $this->feuilleRepository->getFeuillesEnfant($feuille->id);
                foreach ($enfants as $feuilleEnfant) {
                    if ($feuilleEnfant->id === null) {
                        continue;
                    }

                    foreach ($this->feuilleRepository->getFeuillesEnfant($feuilleEnfant->id) as $feuilleEnfant2) {
                        if (str_contains($currentUri, (string) $feuilleEnfant2->lien)) {
                            $isCurrent = true;
                        }
                    }
                }
            }

            $feuilles[] = [
                'isCurrent' => $isCurrent,
                'lien' => $feuille->lien,
                'nom' => $feuille->nom,
            ];
        }

        return $feuilles;
    }
}
