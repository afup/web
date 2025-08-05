<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Configuration;

use AppBundle\Accounting\Model\Repository\OperationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ListOperationAction
{
    public function __construct(
        private readonly OperationRepository $accountRepository,
        private readonly Environment $twig,
    ) {}

    public function __invoke(Request $request): Response
    {
        $operations = $this->accountRepository->getAll();

        return new Response($this->twig->render('admin/accounting/configuration/operation_list.html.twig', [
            'operations' => $operations,
        ]));
    }
}
