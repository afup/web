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
    public function __construct(
        private readonly ViewRenderer $view,
        private readonly RepositoryFactory $repositoryFactory,
        private readonly string $mailchimpTechletterWebhookKey,
    ) {
    }

    public function index(): Response
    {
        return $this->view->render('site/techletter/index.html.twig');
    }

    public function webhook(Request $request): Response
    {
        if ($request->get('webhook_key') !== $this->mailchimpTechletterWebhookKey) {
            return new Response('ko', Response::HTTP_UNAUTHORIZED);
        }

        if (Request::METHOD_GET === $request->getMethod()) {
            return new Response('ok');
        }

        if ($request->get('type') === 'unsubscribe') {
            $techletterUnsubscriptionRepository = $this->repositoryFactory->get(TechletterUnsubscriptionsRepository::class);
            $techletterUnsubscription = $techletterUnsubscriptionRepository->createFromWebhookData($request->get('data', []));
            $techletterUnsubscriptionRepository->save($techletterUnsubscription);
        }

        return new Response('ok');
    }
}
