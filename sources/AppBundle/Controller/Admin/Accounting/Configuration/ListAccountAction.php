<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Configuration;

use AppBundle\Accounting\Model\Repository\AccountRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ListAccountAction
{
    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly Environment $twig,
    ) {}

    public function __invoke(Request $request): Response
    {
        $accounts = $this->accountRepository->getAll();

        return new Response($this->twig->render('admin/accounting/configuration/account_list.html.twig', [
            'accounts' => $accounts,
        ]));
    }
}
