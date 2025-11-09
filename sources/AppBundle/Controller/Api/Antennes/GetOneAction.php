<?php

declare(strict_types=1);

namespace AppBundle\Controller\Api\Antennes;

use AppBundle\Antennes\AntennesCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class GetOneAction
{
    public function __construct(
        private AntennesCollection $antennesCollection,
    ) {}

    public function __invoke(string $code): JsonResponse
    {
        try {
            $antenne = $this->antennesCollection->findByCode($code);
        } catch (\InvalidArgumentException) {
            throw new NotFoundHttpException();
        }

        if ($antenne->hideOnOfficesPage) {
            throw new NotFoundHttpException();
        }

        return new JsonResponse([
            'code' => $antenne->code,
            'label' => $antenne->label,
            'links' => [
                'meetup' => $this->createLink('https://www.meetup.com/fr-FR/', $antenne->meetup->urlName),
                'linkedin' => $this->createLink('https://www.linkedin.com/company/', $antenne->socials->linkedin),
                'bluesky' => $this->createLink('https://bsky.app/profile/', $antenne->socials->bluesky),
            ],
        ]);
    }

    private function createLink(string $prefix, ?string $suffix): ?string
    {
        if ($suffix === null) {
            return null;
        }

        return $prefix . $suffix;
    }
}
