<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Configuration;

use AppBundle\Accounting\Entity\Repository\RuleRepository;
use AppBundle\Accounting\Entity\Rule;
use AppBundle\Accounting\Form\RuleType;
use AppBundle\AuditLog\Audit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AddRuleAction extends AbstractController
{
    public function __construct(
        private readonly RuleRepository $ruleRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request): Response
    {
        $rule = new Rule();
        $form = $this->createForm(RuleType::class, $rule);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->ruleRepository->save($rule);
            $this->audit->log('Ajout de la règle ' . $rule->label);
            $this->addFlash('notice', 'La règle a été ajoutée');
            return $this->redirectToRoute('admin_accounting_rules_list');
        }

        return $this->render('admin/accounting/configuration/rule_add.html.twig', [
            'form' => $form->createView(),
            'rule' => $rule,
            'formTitle' => 'Ajouter une règle',
            'submitLabel' => 'Ajouter',
        ]);
    }
}
