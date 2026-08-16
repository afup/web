<?php

declare(strict_types=1);

namespace AppBundle\Twig\Components;

use Afup\Site\Corporate\Feuille;
use AppBundle\Security\Authentication;
use AppBundle\Site\Entity\Feuille as FeuilleEntity;
use AppBundle\Site\Entity\Repository\FeuilleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class MainMenu
{
    public ?int $currentFeuilleId = null;

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly FeuilleRepository $feuilleRepository,
        private readonly Authentication $authentication,
    ) {}

    /**
     * @return array{main: array<array{isCurrent: boolean, feuille: FeuilleEntity}>, sub: array<array{feuille: FeuilleEntity, is_active: boolean}>}
     */
    public function getEntries(): array
    {
        $feuillesEnfants = $this->feuilleRepository->getFeuillesEnfant(Feuille::ID_FEUILLE_HEADER);
        $request = $this->requestStack->getMainRequest();

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

        $currentUri = $request?->getRequestUri() ?? '';
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
                'feuille' => $feuille,
            ];
        }

        $subEntries = [];
        if ($this->currentFeuilleId !== null && $request !== null) {
            $menu = $this->feuilleRepository->getFeuillesEnfant($this->currentFeuilleId);

            foreach ($menu as $feuille) {
                $subEntries[] = [
                    'feuille' => $feuille,
                    'is_active' => $this->isSubEntryActive($request, $feuille),
                ];
            }
        }

        return [
            'main' => $feuilles,
            'sub' => $subEntries,
        ];
    }

    private function isSubEntryActive(Request $request, FeuilleEntity $feuille): bool
    {
        $url = $request->getUri();

        $pattern = '/' . preg_quote((string) $feuille->lien, '/') . '/';

        if (preg_match($pattern, $url)) {
            return true;
        }

        if ($feuille->patterns) {
            foreach (explode(PHP_EOL, $feuille->patterns) as $pattern) {
                $pattern = trim($pattern);
                if ($pattern === '') {
                    continue;
                }

                if (preg_match($pattern, $url)) {
                    return true;
                }
            }
        }

        return false;
    }
}
