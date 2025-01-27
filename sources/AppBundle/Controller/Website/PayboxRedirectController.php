<?php

namespace AppBundle\Controller\Website;

use AppBundle\Payment\PayboxResponseFactory;
use AppBundle\WebsiteBlocks;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PayboxRedirectController extends Controller
{
    private WebsiteBlocks $websiteBlocks;

    public function __construct(WebsiteBlocks $websiteBlocks)
    {
        $this->websiteBlocks = $websiteBlocks;
    }

    public function indexAction(Request $request, $type = 'success')
    {
        $payboxResponse = PayboxResponseFactory::createFromRequest($request);

        return $this->websiteBlocks->render('site/company_membership/paybox_redirect.html.twig', [
            'payboxResponse' => $payboxResponse,
            'status' => $request->get('status'),
            'return_type' => $type
        ]);
    }
}
