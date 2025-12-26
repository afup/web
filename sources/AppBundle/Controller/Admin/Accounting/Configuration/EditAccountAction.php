<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Configuration;

use AppBundle\Accounting\Form\AccountType;
use AppBundle\Accounting\Model\Repository\AccountRepository;
use AppBundle\AuditLog\Audit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class EditAccountAction extends AbstractController
{
    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(int $id,Request $request): Response
    {
        $account = $this->accountRepository->get($id);
        $form = $this->createForm(AccountType::class, $account);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->accountRepository->save($account);
            $this->audit->log('Modification du compte ' . $account->getName());
            $this->addFlash('notice', 'Le compte ' . $account->getName() . ' a été modifié');
            return $this->redirectToRoute('admin_accounting_accounts_list');
        }

        return $this->render('admin/accounting/configuration/account_edit.html.twig', [
            'form' => $form->createView(),
            'account' => $account,
            'formTitle' => 'Modifier un compte',
            'submitLabel' => 'Modifier',
        ]);
    }
}
