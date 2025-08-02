<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Configuration;

use AppBundle\Accounting\Model\Account;
use AppBundle\Accounting\Model\Repository\AccountRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class ArchiveAccountAction extends AbstractController
{
    public function __construct(private readonly AccountRepository $accountRepository) {}

    public function __invoke(int $id): RedirectResponse
    {
        $account = $this->accountRepository->get($id);

        if (!$account instanceof Account) {
            $this->addFlash('error', 'Compte non trouvé');

            return $this->redirect('/pages/administration/index.php?page=compta_conf_compte&action=lister');
        }

        $account->setArchivedAt(new \DateTime());
        $this->accountRepository->save($account);

        return $this->redirectToRoute('admin_accounting_accounts_edit', [
            'id' => $id,
        ]);
    }
}
