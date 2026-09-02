<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Journal;

use AppBundle\Accounting\Entity\Repository\CategoryRepository;
use AppBundle\Accounting\Entity\Repository\EventRepository;
use AppBundle\Accounting\Entity\Repository\PaymentRepository;
use AppBundle\Accounting\Entity\Repository\TransactionRepository;
use AppBundle\Accounting\Entity\Transaction;
use AppBundle\Accounting\TransactionModification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateInfoAction extends AbstractController
{
    public function __construct(
        private readonly TransactionRepository $transactionRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly PaymentRepository $paymentRepository,
        private readonly EventRepository $eventRepository,
    ) {}

    public function __invoke(
        Request $request,
        int $id,
        TransactionModification $modification,
    ): JsonResponse {
        $transaction = $this->transactionRepository->find($id);
        if (!$transaction instanceof Transaction) {
            throw $this->createNotFoundException();
        }

        try {
            $value = $request->getPayload()->get('val');
            if (!$modification->allowsEmpty() && ($value === null || $value === '')) {
                throw new \InvalidArgumentException(sprintf('Value cannot be empty for "%s"', $modification->value));
            }

            $modification->setValue($transaction, $value, $this->categoryRepository, $this->paymentRepository, $this->eventRepository);
            $this->transactionRepository->save($transaction);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['success' => true]);
    }
}
