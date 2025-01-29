<?php

namespace AppBundle\Controller\Website;

use AppBundle\Payment\PayboxResponseFactory;
use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PayboxRedirectController extends Controller
{
    private ViewRenderer $view;

    public function __construct(ViewRenderer $view)
    {
        $this->view = $view;
    }

    public function indexAction(Request $request, $type = 'success')
    {
        $payboxResponse = PayboxResponseFactory::createFromRequest($request);

        return $this->view->render('site/company_membership/paybox_redirect.html.twig', [
            'payboxResponse' => $payboxResponse,
            'status' => $request->get('status'),
            'return_type' => $type
        ]);
    }
}
