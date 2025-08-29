<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Configuration;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Accounting\Form\AccountType;
use AppBundle\Accounting\Model\Repository\AccountRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class EditAccountAction extends AbstractController
{
    use DbLoggerTrait;

    public function __construct(
        private readonly AccountRepository $accountRepository,
    ) {}

    public function __invoke(int $id,Request $request): Response
    {
        $account = $this->accountRepository->get($id);
        $form = $this->createForm(AccountType::class, $account);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->accountRepository->save($account);
            $this->log('Modification du compte ' . $account->getName());
            $this->addFlash('notice', 'Le compte ' . $account->getName() . ' a été modifié');
            return $this->redirect('/pages/administration/index.php?page=compta_conf_compte&action=lister&filtre=' . $account->getName());
        }

        return $this->render('admin/accounting/configuration/account_edit.html.twig', [
            'form' => $form->createView(),
            'account' => $account,
            'formTitle' => 'Modifier un compte',
            'submitLabel' => 'Modifier',
        ]);
    }
}
