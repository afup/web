<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Configuration;

use AppBundle\Accounting\Form\AccountType;
use AppBundle\Accounting\Entity\Account;
use AppBundle\Accounting\Entity\Repository\AccountRepository;
use AppBundle\AuditLog\Audit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AddAccountAction extends AbstractController
{
    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request): Response
    {
        $account = new Account();
        $form = $this->createForm(AccountType::class, $account);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->accountRepository->save($account);
            $this->audit->log('Ajout du compte ' . $account->name);
            $this->addFlash('notice', 'Le compte ' . $account->name . ' a été créé');
            return $this->redirectToRoute('admin_accounting_accounts_list');
        }

        return $this->render('admin/accounting/configuration/account_add.html.twig', [
            'form' => $form->createView(),
            'account' => $account,
            'formTitle' => 'Ajouter un compte',
            'submitLabel' => 'Ajouter',
        ]);
    }
}
