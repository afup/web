<?php

declare(strict_types=1);

namespace AppBundle\Accounting;

use AppBundle\Accounting\Model\Transaction;

enum TransactionModification: string
{
    case Category = 'category';
    case PaymentType = 'paymentType';
    case Event = 'event';
    case Comment = 'comment';
    case RequiredAttachment = 'requiredAttachment';

    public function setValue(Transaction $transaction, mixed $value): void
    {
        match ($this) {
            self::Category => $transaction->setCategoryId((int) $value),
            self::PaymentType => $transaction->setPaymentTypeId((int) $value),
            self::Event => $transaction->setEventId((int) $value),
            self::Comment => $transaction->setComment($value),
            self::RequiredAttachment => $transaction->setAttachmentRequired((bool) $value),
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
