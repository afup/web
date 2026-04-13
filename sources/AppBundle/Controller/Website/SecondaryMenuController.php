<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website;

use AppBundle\Site\Entity\Feuille;
use AppBundle\Site\Entity\Repository\FeuilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class SecondaryMenuController extends AbstractController
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly FeuilleRepository $feuilleRepository,
    ) {}

    public function display(Request $request): Response
    {
        $menu = $this->feuilleRepository->getFeuillesEnfant($request->get('feuille_id'));

        return $this->render(
            'site/secondary_menu.html.twig',
            [
                'menu' => $this->prepareMenu($this->requestStack->getMainRequest(), $menu),
            ],
        );
    }

    /**
     * @param Feuille[] $menu
     * @return mixed[]
     */
    protected function prepareMenu(Request $masterRequest, array $menu): array
    {
        $preparedMenu = [];

        foreach ($menu as $feuille) {
            $preparedMenu[] = [
                'lien' => $feuille->lien,
                'nom' => $feuille->nom,
                'patterns' => $feuille->patterns,
                'is_active' => $this->isActive($masterRequest, $feuille),
            ];
        }

        return $preparedMenu;
    }

    private function isActive(Request $masterRequest, Feuille $feuille): bool
    {
        $url = $masterRequest->getUri();

        $pattern = '/' . preg_quote((string) $feuille->lien, '/') . '/';

        if (preg_match($pattern, $url)) {
            return true;
        }

        if ($feuille->patterns) {
            foreach (explode(PHP_EOL, (string) $feuille->patterns) as $pattern) {
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
