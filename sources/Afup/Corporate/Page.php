<?php

declare(strict_types=1);

namespace Afup\Site\Corporate;

use AppBundle\Site\Entity\Feuille as FeuilleEntity;
use AppBundle\Site\Entity\Repository\FeuilleRepository;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class Page
{
    public function __construct(
        private FeuilleRepository $feuilleRepository,
    ) {}

    public function header($url = null, UserInterface $user = null): string
    {
        $url = urldecode((string) $url);
        $str = '<ul>';

        $feuillesEnfants = $this->feuilleRepository->getFeuillesEnfant(Feuille::ID_FEUILLE_HEADER);

        $cssClasses = [];
        $feuilleLogin = new FeuilleEntity();
        $feuilleLogin->idParent = Feuille::ID_FEUILLE_HEADER;
        $feuilleLogin->lien = '/member';
        $feuilleLogin->alt = '';
        $feuilleLogin->position = 999;
        $feuilleLogin->etat = 1;

        if ($user instanceof UserInterface) {
            $feuilleLogin->id = PHP_INT_MAX;
            $feuilleLogin->nom = 'Espace membre';
            $feuilleLogin->patterns = "#/admin/company#";
        } else {
            $feuilleLogin = new FeuilleEntity();
            $feuilleLogin->id = PHP_INT_MAX - 1;
            $feuilleLogin->nom = 'Se connecter';
            $cssClasses[PHP_INT_MAX - 1] = 'desktop-hidden';
        }

        $feuillesEnfants[] = $feuilleLogin;

        foreach ($feuillesEnfants as $feuille) {
            $isCurrent = false;
            if ($feuille->patterns) {
                foreach (explode(PHP_EOL, (string) $feuille->patterns) as $pattern) {
                    $pattern = trim($pattern);
                    if ($pattern === '') {
                        continue;
                    }

                    if (preg_match($pattern, $url)) {
                        $isCurrent = true;
                    }
                }
            }

            if (str_contains($url, (string) $feuille->lien)) {
                $isCurrent = true;
            }

            if (false === $isCurrent) {
                $enfants = $this->feuilleRepository->getFeuillesEnfant($feuille->id);
                foreach ($enfants as $feuilleEnfant) {
                    foreach ($this->feuilleRepository->getFeuillesEnfant($feuilleEnfant->id) as $feuilleEnfant2) {
                        if (str_contains($url, (string) $feuilleEnfant2->lien)) {
                            $isCurrent = true;
                        }
                    }
                }
            }

            $class = $isCurrent ? " subheader-current " : "";

            if (isset($cssClasses[$feuille->id])) {
                $class .= ' ' . $cssClasses[$feuille->id];
            }

            $str .= sprintf("<li class='%s'><a href='%s'>%s</a></li>", $class, $feuille->lien, $feuille->nom);
        }

        return $str . '<ul>';
    }

    /**
     * @return array{nom: mixed, items: FeuilleEntity[]}[]
     */
    public function footer(): array
    {
        $footerColumns = [];
        foreach ($this->feuilleRepository->getFeuillesEnfant(Feuille::ID_FEUILLE_FOOTER) as $feuilleColonne) {
            $footerColumns[] = [
                'nom' => $feuilleColonne->nom,
                'items' => $this->feuilleRepository->getFeuillesEnfant($feuilleColonne->id),
            ];
        }

        return $footerColumns;
    }
}
