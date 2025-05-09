<?php

declare(strict_types=1);


namespace AppBundle\Controller\Website;

use AppBundle\Antennes\AntennesCollection;
use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StaticController extends AbstractController
{
    public function __construct(
        private readonly ViewRenderer $view,
        private readonly string $superAperoCsvUrl,
    ) {
    }

    public function offices(): Response
    {
        return $this->view->render('site/offices.html.twig', [
            'antennes' => (new AntennesCollection())->getAllSortedByLabels(),
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
            if (trim((string) $row[0]) === '') {
                continue;
            }

            [$code, $meeetupId, $content] = $row;

            $apero = [
                'code' => mb_strtolower((string) $code),
                'content' => $content,
            ];

            if (strlen(trim((string) $meeetupId)) !== 0) {
                $apero['meetup_id'] = $meeetupId;
            }

            $aperos[] = $apero;
        }

        return $aperos;
    }

    public function superApero(): Response
    {
        return $this->view->render('site/superapero.html.twig', [
            'aperos' => $this->getAperos($this->superAperoCsvUrl),
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
