<?php

namespace AppBundle\Controller;

use AppBundle\Payment\PayboxResponseFactory;
use Symfony\Component\HttpFoundation\Request;

class PayboxRedirectAction extends SiteBaseController
{
    public function index(Request $request, $type = 'success')
    {
        $payboxResponse = PayboxResponseFactory::createFromRequest($request);

        return $this->render('site/company_membership/paybox_redirect.html.twig', [
            'payboxResponse' => $payboxResponse,
            'status' => $request->get('status'),
            'return_type' => $type
        ]);
    }
}
