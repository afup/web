<?php

declare(strict_types=1);

namespace AppBundle\Payment;

class PayboxResponse
{
    public const STATUS_SUCCESS = '00000';

    public const STATUS_CANCELED = '';

    public const STATUS_ERROR = '';

    public const STATUS_DUPLICATE = '00015';

    /**
     * @param string $cmd
     * @param string $status
     * @param int $total
     * @param string $authorizationId
     * @param string $transactionId
     */
    public function __construct(
        private $cmd,
        private $status,
        private $total,
        private $authorizationId,
        private $transactionId,
    ) {}

    /**
     * @return string
     */
    public function getCmd()
    {
        return $this->cmd;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @return string
     */
    public function getAuthorizationId()
    {
        return $this->authorizationId;
    }

    /**
     * @return string
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    public function isSuccessful(): bool
    {
        return $this->status === self::STATUS_SUCCESS;
    }

    public function isErrorCode(): bool
    {
        return str_starts_with($this->status, '001');
    }
}
