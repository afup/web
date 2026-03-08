<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Configuration;

use AppBundle\Accounting\Entity\Repository\OperationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ListOperationAction
{
    public function __construct(
        private readonly OperationRepository $operationRepository,
        private readonly Environment $twig,
    ) {}

    public function __invoke(Request $request): Response
    {
        $operations = $this->operationRepository->findAll();

        return new Response($this->twig->render('admin/accounting/configuration/operation_list.html.twig', [
            'operations' => $operations,
        ]));
    }
}
