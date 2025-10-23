<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Configuration;

use AppBundle\Accounting\Model\Repository\RuleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ListRuleAction
{
    public function __construct(
        private readonly RuleRepository $ruleRepository,
        private readonly Environment $twig,
    ) {}

    public function __invoke(Request $request): Response
    {
        $rules = $this->ruleRepository->getAllSortedByName();

        return new Response($this->twig->render('admin/accounting/configuration/rule_list.html.twig', [
            'rules' => $rules,
        ]));
    }
}
