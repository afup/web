<?php

declare(strict_types=1);

namespace Afup\Site\Corporate;

use AppBundle\Site\Model\Repository\SheetRepository;
use AppBundle\Site\Model\Sheet;
use CCMBenchmark\Ting\Repository\Collection;

class Branche
{
    public $navigation = 'nom';

    public function __construct(private readonly SheetRepository $sheetRepository) {}

    public function navigation_avec_image($bool = false): void
    {
        if ($bool) {
            $this->navigation = 'image';
        }
    }

    public function naviguer($id, $profondeur = 1, string $identification = ""): string
    {
        $racine = $this->sheetRepository->getOneBy(['id' => $id, 'state' => 1]);
        if (!$racine instanceof Sheet) {
            return '';
        }

        $feuilles = $this->extraireFeuilles($id, $profondeur);
        if ($feuilles !== '' && $feuilles !== '0') {
            $navigation = '<ul id="' . $identification . '" class="' . Site::raccourcir($racine['nom']) . '">' . $feuilles . '</ul>';
        } else {
            $navigation = '';
        }

        return $navigation;
    }

    public function extraireFeuilles($id, $profondeur): string
    {
        $extraction = '';
        $sheets = $this->sheetRepository->getActiveChildrenByParentIdOrderedByPostion($id);
        if (!$sheets instanceof Collection) {
            return $extraction;
        }

        foreach ($sheets as $feuille) {
            $class = "";
            if ($extraction === "") {
                $class = ' class="top"';
            }
            $route = match (true) {
                preg_match('#^http://#', (string) $feuille['lien']), preg_match('#^/#', (string) $feuille['lien']) => $feuille['lien'],
                default => Site::WEB_PATH . Site::WEB_PREFIX . Site::WEB_QUERY_PREFIX . $feuille['lien'],
            };
            $extraction .= '<li' . $class . '>';
            if ($this->navigation == 'image' && $feuille['image'] !== null) {
                $extraction .= '<a href="' . $route . '"><img alt="' . $feuille['alt'] . '" src="' . Site::WEB_PATH . 'templates/site/images/' . $feuille['image'] . '" /><br>';
                $extraction .= $feuille['nom'] . '</a><br>';
                $extraction .= $feuille['alt'];
            } else {
                $extraction .= '<a href="' . $route . '">' . $feuille['nom'] . '</a>';
            }
            $extraction .= '</li>';
            if ($profondeur > 0) {
                $extraction .= $this->naviguer($feuille['id'], $profondeur - 1);
            }
        }

        return $extraction;
    }
}
