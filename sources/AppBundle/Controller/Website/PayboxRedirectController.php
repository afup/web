<?php

namespace AppBundle\Controller\Website;

use AppBundle\Payment\PayboxResponseFactory;
use Symfony\Component\HttpFoundation\Request;

class PayboxRedirectController extends SiteBaseController
{
    public function indexAction(Request $request, $type = 'success')
    {
        $payboxResponse = PayboxResponseFactory::createFromRequest($request);

        return $this->render('site/company_membership/paybox_redirect.html.twig', [
            'payboxResponse' => $payboxResponse,
            'status' => $request->get('status'),
            'return_type' => $type
        ]);
    }
}
