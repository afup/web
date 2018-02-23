<?php


namespace AppBundle\Controller;

use AppBundle\Offices\OfficesCollection;
use Symfony\Component\HttpFoundation\Request;

class StaticController extends SiteBaseController
{
    public function officesAction()
    {
        $officesCollection = new OfficesCollection();
        return $this->render(
        ':site:offices.html.twig',
            [
                'offices' => $officesCollection->getAllSortedByLabels()
            ]
        );
    }

    public function superAperoAction()
    {
        return $this->render(':site:superapero.html.twig');
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
