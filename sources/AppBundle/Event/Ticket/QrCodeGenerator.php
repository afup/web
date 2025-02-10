<?php

declare(strict_types=1);

namespace AppBundle\Event\Ticket;

class QrCodeGenerator
{
    private string $salt;

    public function __construct(string $salt)
    {
        $this->salt = $salt;
    }

    public function generate(int $idTicket): string
    {
        return strtr(
            substr(md5($idTicket . $this->salt), 0, 5),
            ['0' => 'e', 'o' => 'b', 'i' => 'c', 'l' => 'd']
        );
    }
}
