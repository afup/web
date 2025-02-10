<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website;

use AppBundle\Association\Model\Repository\TechletterUnsubscriptionsRepository;
use AppBundle\Twig\ViewRenderer;
use CCMBenchmark\TingBundle\Repository\RepositoryFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TechletterController extends AbstractController
{
    private ViewRenderer $view;
    private RepositoryFactory $repositoryFactory;
    private string $mailchimpTechletterWebhookKey;

    public function __construct(ViewRenderer $view,
                                RepositoryFactory $repositoryFactory,
                                string $mailchimpTechletterWebhookKey)
    {
        $this->view = $view;
        $this->repositoryFactory = $repositoryFactory;
        $this->mailchimpTechletterWebhookKey = $mailchimpTechletterWebhookKey;
    }

    public function index(): Response
    {
        return $this->view->render('site/techletter/index.html.twig');
    }

    /**
     * @return Response
     */
    public function webhook(Request $request)
    {
        if ($request->get('webhook_key') !== $this->mailchimpTechletterWebhookKey) {
            return new Response('ko', Response::HTTP_UNAUTHORIZED);
        }

        if (Request::METHOD_GET == $request->getMethod()) {
            return new Response('ok');
        }

        if ($request->get('type') == 'unsubscribe') {
            $techletterUnsubscriptionRepository = $this->repositoryFactory->get(TechletterUnsubscriptionsRepository::class);
            $techletterUnsubscription = $techletterUnsubscriptionRepository->createFromWebhookData($request->get('data', []));
            $techletterUnsubscriptionRepository->save($techletterUnsubscription);
        }

        return new Response('ok');
    }
}
