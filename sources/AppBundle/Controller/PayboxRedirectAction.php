<?php

namespace AppBundle\Controller;

use AppBundle\Payment\PayboxResponseFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class PayboxRedirectAction
{
    /**
     * @var Environment
     */
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function __invoke(Request $request)
    {
        $payboxResponse = PayboxResponseFactory::createFromRequest($request);

        return new Response($this->twig->render('site/company_membership/paybox_redirect.html.twig', [
            'payboxResponse' => $payboxResponse,
            'status' => $request->get('status')
        ]));
    }
}
