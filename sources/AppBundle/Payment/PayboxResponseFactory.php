<?php

declare(strict_types=1);

namespace AppBundle\Payment;

use Symfony\Component\HttpFoundation\Request;

class PayboxResponseFactory
{
    public static function createFromRequest(Request $request): PayboxResponse
    {
        $keys = [
            'cmd',
            'transaction',
            'status',
        ];
        foreach ($keys as $key) {
            if ($request->query->has($key) === false) {
                throw new \RuntimeException(sprintf('Required parameter "%s" not found', $key));
            }
        }
        $query = $request->query;
        return new PayboxResponse(
            $query->get('cmd'),
            $query->get('status'),
            $query->get('total'),
            $query->get('autorisation'),
            $query->get('transaction')
        );
    }
}
