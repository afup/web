<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PagerController extends AbstractController
{
    const PAGER_LINKS_COUNT = 6;

    public function display(Request $request): Response
    {
        $totalItems = $request->get('total_items');
        $currentPage = $request->get('current_page');
        $itemsPerPage = $request->get('items_per_page');

        $nbPages = floor($totalItems / $itemsPerPage);

        $displayedPages = [];
        $fistPageItem = max($currentPage - (self::PAGER_LINKS_COUNT / 2), 1);

        for ($i=0; $i<=self::PAGER_LINKS_COUNT; $i++) {
            if (($fistPageItem + $i) > $nbPages) {
                continue;
            }
            $displayedPages[] = $fistPageItem + $i;
        }

        return $this->render(
            ':site:pager.html.twig',
            [
                'nb_pages' => $nbPages,
                'displayed_pages' => $displayedPages,
                'current_page' => $currentPage,
                'extra_parameters' => $request->get('extra_parameters', []),
            ]
        );
    }
}
