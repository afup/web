<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Configuration;

use AppBundle\Accounting\Entity\Account;
use AppBundle\Accounting\Entity\Repository\AccountRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_COMPTA_WRITER')]
final class RestoreAccountAction extends AbstractController
{
    public function __construct(private readonly AccountRepository $accountRepository) {}

    public function __invoke(int $id): RedirectResponse
    {
        $account = $this->accountRepository->find($id);

        if (!$account instanceof Account) {
            $this->addFlash('error', 'Compte non trouvé');

            return $this->redirectToRoute('admin_accounting_accounts_list');
        }

        $account->archivedAt = null;
        $this->accountRepository->save($account);

        return $this->redirectToRoute('admin_accounting_accounts_edit', [
            'id' => $id,
        ]);
    }
}
