<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website;

use Afup\Site\Corporate\Branche;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class SecondaryMenuController extends AbstractController
{
    public function __construct(private readonly RequestStack $requestStack)
    {
    }
    public function display(Request $request): Response
    {
        $branche = new Branche();
        $menu = $branche->feuillesEnfants($request->get('feuille_id'));

        return $this->render(
            'site/secondary_menu.html.twig',
            [
                'menu' => $this->prepareMenu($this->requestStack->getMainRequest(), $menu),
            ]
        );
    }

    /**
     * @return mixed[]
     */
    protected function prepareMenu(Request $masterRequest, array $menu): array
    {
        $preparedMenu = [];

        foreach ($menu as $feuille) {
            $feuille['is_active'] = $this->isActive($masterRequest, $feuille);
            $preparedMenu[] = $feuille;
        }

        return $preparedMenu;
    }

    private function isActive(Request $masterRequest, array $feuille)
    {
        $url = $masterRequest->getUri();

        $pattern = '/' . preg_quote((string) $feuille['lien'], '/') . '/';

        if (preg_match($pattern, $url)) {
            return true;
        }

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

        return $isCurrent;
    }
}
