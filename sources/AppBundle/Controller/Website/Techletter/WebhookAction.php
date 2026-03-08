<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Techletter;

use AppBundle\Association\Model\Repository\TechletterUnsubscriptionsRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class WebhookAction
{
    public function __construct(
        private TechletterUnsubscriptionsRepository $techletterUnsubscriptionsRepository,
        #[Autowire('%mailchimp_techletter_webhook_key%')]
        private string $mailchimpTechletterWebhookKey,
    ) {}

    public function __invoke(Request $request): Response
    {
        if ($request->get('webhook_key') !== $this->mailchimpTechletterWebhookKey) {
            return new Response('ko', Response::HTTP_UNAUTHORIZED);
        }

        if (Request::METHOD_GET === $request->getMethod()) {
            return new Response('ok');
        }

        if ($request->get('type') === 'unsubscribe') {
            $techletterUnsubscription = $this->techletterUnsubscriptionsRepository->createFromWebhookData($request->get('data', []));
            $this->techletterUnsubscriptionsRepository->save($techletterUnsubscription);
        }

        return new Response('ok');
    }
}
