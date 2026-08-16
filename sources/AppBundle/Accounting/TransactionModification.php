<?php

declare(strict_types=1);

namespace AppBundle\Accounting;

use AppBundle\Accounting\Entity\Repository\CategoryRepository;
use AppBundle\Accounting\Entity\Repository\EventRepository;
use AppBundle\Accounting\Entity\Repository\PaymentRepository;
use AppBundle\Accounting\Entity\Transaction;

enum TransactionModification: string
{
    case Category = 'category';
    case PaymentType = 'paymentType';
    case Event = 'event';
    case Comment = 'comment';
    case RequiredAttachment = 'requiredAttachment';

    public function setValue(
        Transaction $transaction,
        mixed $value,
        CategoryRepository $categoryRepository,
        PaymentRepository $paymentRepository,
        EventRepository $eventRepository,
    ): void {
        match ($this) {
            self::Category => $transaction->categorie = $categoryRepository->find((int) $value),
            self::PaymentType => $transaction->modeReglement = $paymentRepository->find((int) $value),
            self::Event => $transaction->evenement = $eventRepository->find((int) $value),
            self::Comment => $transaction->commentaire = $value,
            self::RequiredAttachment => $transaction->justificatifRequis = (bool) $value,
        };
    }

    public function allowsEmpty(): bool
    {
        return match ($this) {
            self::Comment, self::RequiredAttachment => true,
            default => false,
        };
    }
}
