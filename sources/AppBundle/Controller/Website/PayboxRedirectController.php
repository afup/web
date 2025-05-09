<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website;

use AppBundle\Payment\PayboxResponseFactory;
use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PayboxRedirectController extends AbstractController
{
    public function __construct(private readonly ViewRenderer $view)
    {
    }

    public function index(Request $request, $type = 'success'): Response
    {
        $payboxResponse = PayboxResponseFactory::createFromRequest($request);

        return $this->view->render('site/company_membership/paybox_redirect.html.twig', [
            'payboxResponse' => $payboxResponse,
            'status' => $request->get('status'),
            'return_type' => $type,
        ]);
    }
}
