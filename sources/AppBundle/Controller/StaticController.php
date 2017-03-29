<?php


namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class StaticController extends SiteBaseController
{
    public function officesAction()
    {
        return $this->render(':site:offices.html.twig');
    }

    public function superAperoAction()
    {
        return $this->render(':site:superapero.html.twig');
    }

    public function superAperoLiveAction()
    {
        return $this->render(':site:superapero_live.html.twig');
    }

    public function voidAction(Request $request)
    {
        $params = [];
        if ($request->attributes->has('legacyContent')) {
            $params = $request->attributes->get('legacyContent');
        }

        return $this->render('site/base.html.twig', $params);
    }
}
