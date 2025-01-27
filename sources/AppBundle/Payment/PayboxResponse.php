<?php

declare(strict_types=1);

namespace AppBundle\Payment;

class PayboxResponse
{
    const STATUS_SUCCESS = '00000';

    const STATUS_CANCELED = '';

    const STATUS_ERROR = '';

    const STATUS_DUPLICATE = '00015';

    /**
     * @var string
     */
    private $cmd;

    /**
     * @var string
     */
    private $status;

    /**
     * @var int
     */
    private $total;

    /**
     * @var string
     */
    private $authorizationId;

    /**
     * @var string
     */
    private $transactionId;

    public function __construct($cmd, $status, $total, $authorizationId, $transactionId)
    {
        $this->cmd = $cmd;
        $this->status = $status;
        $this->total = $total;
        $this->authorizationId = $authorizationId;
        $this->transactionId = $transactionId;
    }

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
        return substr($this->status, 0, 3) === '001';
    }
}
