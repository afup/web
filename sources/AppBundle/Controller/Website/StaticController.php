<?php

declare(strict_types=1);


namespace AppBundle\Controller\Website;

use AppBundle\Offices\OfficesCollection;
use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StaticController extends AbstractController
{
    private ViewRenderer $view;
    private string $superAperoCsvUrl;

    public function __construct(ViewRenderer $view, string $superAperoCsvUrl)
    {
        $this->view = $view;
        $this->superAperoCsvUrl = $superAperoCsvUrl;
    }

    public function offices(): Response
    {
        $officesCollection = new OfficesCollection();
        return $this->view->render(':site:offices.html.twig', [
            'offices' => $officesCollection->getAllSortedByLabels()
        ]);
    }

    /**
     * @return array{code: (string | null), content: (string | null), meetup_id?: (string | null)}[]
     */
    protected function getAperos($url): array
    {
        $fp = fopen($url, 'rb');
        if (!$fp) {
            throw new \RuntimeException("Error opening spreadsheet");
        }

        $aperos = [];

        while (false !== ($row = fgetcsv($fp))) {
            if (trim($row[0]) === '') {
                continue;
            }

            [$code, $meeetupId, $content] = $row;

            $apero = [
                'code' => $code,
                'content' => $content,
            ];

            if (strlen(trim($meeetupId)) !== 0) {
                $apero['meetup_id'] = $meeetupId;
            }

            $aperos[] = $apero;
        }

        return $aperos;
    }

    public function superApero(): Response
    {
        return $this->view->render(':site:superapero.html.twig', [
            'aperos' => $this->superAperoCsvUrl
        ]);
    }

    public function void(Request $request): Response
    {
        $params = [];
        if ($request->attributes->has('legacyContent')) {
            $params = $request->attributes->get('legacyContent');
        }

        return $this->view->render('site/base.html.twig', $params);
    }
}
