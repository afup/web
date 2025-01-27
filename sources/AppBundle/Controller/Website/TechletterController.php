<?php

namespace AppBundle\Controller\Website;

use AppBundle\Association\Model\Repository\TechletterUnsubscriptionsRepository;
use AppBundle\WebsiteBlocks;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TechletterController extends Controller
{
    private WebsiteBlocks $websiteBlocks;

    public function __construct(WebsiteBlocks $websiteBlocks)
    {
        $this->websiteBlocks = $websiteBlocks;
    }

    public function indexAction()
    {
        return $this->websiteBlocks->render('site/techletter/index.html.twig');
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function webhookAction(Request $request)
    {
        if ($request->get('webhook_key') != $this->getParameter('mailchimp_techletter_webhook_key')) {
            return new Response('ko', 401);
        }

        if (Request::METHOD_GET == $request->getMethod()) {
            return new Response('ok');
        }

        if ($request->get('type') == 'unsubscribe') {
            $techletterUnsubscriptionRepository = $this->get('ting')->get(TechletterUnsubscriptionsRepository::class);
            $techletterUnsubscription = $techletterUnsubscriptionRepository->createFromWebhookData($request->get('data', []));
            $techletterUnsubscriptionRepository->save($techletterUnsubscription);
        }

        return new Response('ok');
    }
}
