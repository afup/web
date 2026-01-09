<?php

declare(strict_types=1);

namespace Afup\Site\Corporate;

use AppBundle\Site\Model\Repository\SheetRepository;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class Page
{
    public function __construct(
        private SheetRepository $sheetRepository,
    ) {}

    public function header($url = null, UserInterface $user = null): string
    {
        $url = urldecode((string) $url);
        $str = '<ul>';

        $feuillesEnfants = iterator_to_array($this->sheetRepository->getActiveChildrenByParentId(Feuille::ID_FEUILLE_HEADER));

        if ($user instanceof UserInterface) {
            $feuillesEnfants[] = [
                'id' => PHP_INT_MAX,
                'id_parent' => Feuille::ID_FEUILLE_HEADER,
                'nom' => 'Espace membre',
                'lien' => '/member',
                'alt' => '',
                'position' => '999',
                'date' => null,
                'etat' => '1',
                'image' => null,
                'patterns' => "#/admin/company#",
            ];
        } else {
            $feuillesEnfants[] = [
                'id' => PHP_INT_MAX - 1,
                'id_parent' => Feuille::ID_FEUILLE_HEADER,
                'nom' => 'Se connecter',
                'lien' => '/member',
                'alt' => '',
                'position' => '999',
                'date' => null,
                'etat' => '1',
                'image' => null,
                'patterns' => null,
                'class' => 'desktop-hidden',
            ];
        }

        foreach ($feuillesEnfants as $feuille) {
            $isCurrent = false;
            if ($feuille['patterns']) {
                foreach (explode(PHP_EOL, (string) $feuille['patterns']) as $pattern) {
                    $pattern = trim($pattern);
                    if ($pattern === '') {
                        continue;
                    }

                    if (preg_match($pattern, $url)) {
                        $isCurrent = true;
                    }
                }
            }

            if (str_contains($url, (string) $feuille['lien'])) {
                $isCurrent = true;
            }

            if (false === $isCurrent) {
                $enfants = $this->sheetRepository->getActiveChildrenByParentId($feuille['id']);
                foreach ($enfants as $feuilleEnfant) {
                    foreach ($this->sheetRepository->getActiveChildrenByParentId($feuilleEnfant['id']) as $feuillesEnfant2) {
                        if (str_contains($url, (string) $feuillesEnfant2['lien'])) {
                            $isCurrent = true;
                        }
                    }
                }
            }

            $class = $isCurrent ? " subheader-current " : "";

            if (isset($feuille['class'])) {
                $class .= ' ' . $feuille['class'];
            }

            $str .= sprintf("<li class='%s'><a href='%s'>%s</a></li>", $class, $feuille['lien'], $feuille['nom']);
        }

        return $str . '<ul>';
    }

    /**
     * @return array{nom: mixed, items: mixed}[]
     */
    public function footer(): array
    {
        $footerColumns = [];
        foreach ($this->sheetRepository->getActiveChildrenByParentId(Feuille::ID_FEUILLE_FOOTER) as $feuilleColonne) {
            $footerColumns[] = [
                'nom' => $feuilleColonne['nom'],
                'items' => $this->sheetRepository->getActiveChildrenByParentId($feuilleColonne['id']),
            ];
        }

        return $footerColumns;
    }
}
