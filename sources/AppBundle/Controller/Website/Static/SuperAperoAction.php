<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Static;

use AppBundle\Twig\ViewRenderer;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;

final readonly class SuperAperoAction
{
    public function __construct(
        private ViewRenderer $view,
        #[Autowire('%env(SUPER_APERO_CSV_URL)%')]
        private string $superAperoCsvUrl,
    ) {}

    public function __invoke(): Response
    {
        return $this->view->render('site/superapero.html.twig', [
            'aperos' => $this->getAperos($this->superAperoCsvUrl),
        ]);
    }

    /**
     * @return array{code: (string | null), content: (string | null), meetup_id?: (string | null)}[]
     */
    protected function getAperos(string $url): array
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
}
