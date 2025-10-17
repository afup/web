<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Configuration;

use AppBundle\Accounting\Form\RuleType;
use AppBundle\Accounting\Model\Repository\RuleRepository;
use AppBundle\AuditLog\Audit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class EditRuleAction extends AbstractController
{
    public function __construct(
        private readonly RuleRepository $ruleRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(int $id,Request $request): Response
    {
        $rule = $this->ruleRepository->get($id);
        $form = $this->createForm(RuleType::class, $rule);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->ruleRepository->save($rule);
            $this->audit->log('Modification de la règle ' . $rule->getLabel());
            $this->addFlash('notice', 'La règle a été modifiée');
            return $this->redirectToRoute('admin_accounting_rules_list');
        }

        return $this->render('admin/accounting/configuration/rule_edit.html.twig', [
            'form' => $form->createView(),
            'rule' => $rule,
            'formTitle' => 'Modifier une règle',
            'submitLabel' => 'Modifier',
        ]);
    }
}
