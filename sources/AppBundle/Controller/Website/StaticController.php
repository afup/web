<?php


namespace AppBundle\Controller\Website;

use AppBundle\Offices\OfficesCollection;
use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class StaticController extends Controller
{
    private ViewRenderer $view;

    public function __construct(ViewRenderer $view)
    {
        $this->view = $view;
    }

    public function officesAction()
    {
        $officesCollection = new OfficesCollection();
        return $this->view->render(':site:offices.html.twig', [
            'offices' => $officesCollection->getAllSortedByLabels()
        ]);
    }

    protected function getAperos($url)
    {
        $fp = fopen($url, 'rb');
        if (!$fp) {
            throw new \RuntimeException("Error opening spreadsheet");
        }

        $aperos = [];

        while (false !== ($row = fgetcsv($fp))) {
            if (0 === strlen(trim($row[0]))) {
                continue;
            }

            [$code, $meeetupId, $content] = $row;

            $apero = [
                'code' => $code,
                'content' => $content,
            ];

            if (strlen(trim($meeetupId))) {
                $apero['meetup_id'] = $meeetupId;
            }

            $aperos[] = $apero;
        }

        return $aperos;
    }

    public function superAperoAction()
    {
        $aperos = $this->getAperos($this->getParameter('super_apero_csv_url'));
        return $this->view->render(':site:superapero.html.twig', [
            'aperos' => $aperos
        ]);
    }

    public function voidAction(Request $request)
    {
        $params = [];
        if ($request->attributes->has('legacyContent')) {
            $params = $request->attributes->get('legacyContent');
        }

        return $this->view->render('site/base.html.twig', $params);
    }
}
