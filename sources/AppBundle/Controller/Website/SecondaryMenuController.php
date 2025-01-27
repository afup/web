<?php

namespace AppBundle\Controller\Website;

use Afup\Site\Corporate\Branche;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SecondaryMenuController extends Controller
{
    public function displayAction(Request $request)
    {
        $branche = new Branche();
        $menu = $branche->feuillesEnfants($request->get('feuille_id'));

        return $this->render(
            ':site:secondary_menu.html.twig',
            [
                'menu' => $this->prepareMenu($this->get('request_stack')->getMasterRequest(), $menu),
            ]
        );
    }

    protected function prepareMenu(Request $masterRequest, array $menu)
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

        $pattern = '/' . preg_quote($feuille['lien'], '/') . '/';

        if (preg_match($pattern, $url)) {
            return true;
        }

        $isCurrent = false;
        foreach (explode(PHP_EOL, $feuille['patterns']) as $pattern) {
            $pattern = trim($pattern);
            if (strlen($pattern) === 0) {
                continue;
            }

            if (preg_match($pattern, $url)) {
                $isCurrent = true;
            }
        }

        return $isCurrent;
    }
}
