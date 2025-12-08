<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Site\Sheet;

use AppBundle\Site\Model\Repository\SheetRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ListSheetsAction
{
    public function __construct(
        private readonly SheetRepository $sheetRepository,
        private readonly Environment $twig,
    ) {}

    public function __invoke(Request $request): Response
    {
        $fields = ['date', 'nom', 'etat'];

        $sort = $request->query->get('sort', 'date');
        if (in_array($sort, $fields) === false) {
            $sort = 'date';
        }
        $direction = $request->query->get('direction', 'desc');
        $filter = $request->query->get('filter', '');
        $sheets = $this->sheetRepository->getAllSheets($sort, $direction, $filter);

        return new Response($this->twig->render('admin/site/sheet_list.html.twig', [
            'sheets' => $sheets,
            'filter' => $filter,
            'sort' => $sort,
            'direction' => $direction,
        ]));
    }
}
