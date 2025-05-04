<?php

declare(strict_types=1);

namespace AppBundle\Controller\Planete;

use PlanetePHP\FeedRepository;
use Symfony\Component\HttpFoundation\Response;

final readonly class FeedsController
{
    public function __construct(private FeedRepository $feedRepository)
    {
    }

    public function __invoke(): Response
    {
        $feeds = $this->feedRepository->findActive();

        $data = [];

        foreach ($feeds as $feed) {
            $data[] = [
                'name' => $feed->getName(),
                'url' => $feed->getUrl(),
            ];
        }

        return new Response(json_encode($data), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }
}
