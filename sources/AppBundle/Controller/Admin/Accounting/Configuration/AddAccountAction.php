<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Configuration;

use AppBundle\Accounting\Form\AccountType;
use AppBundle\Accounting\Model\Account;
use AppBundle\Accounting\Model\Repository\AccountRepository;
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
            $this->audit->log('Ajout du compte ' . $account->getName());
            $this->addFlash('notice', 'Le compte ' . $account->getName() . ' a été créé');
            return $this->redirect('/pages/administration/index.php?page=compta_conf_compte&action=lister&filtre=' . $account->getName());
        }

        return $this->render('admin/accounting/configuration/account_add.html.twig', [
            'form' => $form->createView(),
            'account' => $account,
            'formTitle' => 'Ajouter un compte',
            'submitLabel' => 'Ajouter',
        ]);
    }
}
