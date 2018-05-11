<?php

namespace AppBundle\Controller;

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

        foreach ($menu as $item) {
            $pattern = '/' . preg_quote($item['lien'], '/') . '/';
            $isActive = preg_match($pattern, $masterRequest->getUri());
            $item['is_active'] = $isActive;
            $preparedMenu[] = $item;
        }

        return $preparedMenu;
    }
}
