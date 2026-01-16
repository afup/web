<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Configuration;

use AppBundle\Accounting\Entity\Account;
use AppBundle\Accounting\Entity\Repository\AccountRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class ArchiveAccountAction extends AbstractController
{
    public function __construct(private readonly AccountRepository $accountRepository) {}

    public function __invoke(int $id): RedirectResponse
    {
        $account = $this->accountRepository->find($id);

        if (!$account instanceof Account) {
            $this->addFlash('error', 'Compte non trouvé');

            return $this->redirectToRoute('admin_accounting_accounts_list');
        }

        $account->archivedAt = new \DateTimeImmutable();
        $this->accountRepository->save($account);

        return $this->redirectToRoute('admin_accounting_accounts_edit', [
            'id' => $id,
        ]);
    }
}
