<?php

declare(strict_types=1);

namespace AppBundle\Controller\Api\Antennes;

use AppBundle\Antennes\AntenneRepository;
use AppBundle\Event\Model\Repository\MeetupRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class GetOneAction
{
    public function __construct(
        private AntenneRepository $antennesCollection,
        private MeetupRepository $meetupRepository,
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

        $response = [
            'code' => $antenne->code,
            'label' => $antenne->label,
            'logo' => [
                'simple' => 'https://afup.org' . $antenne->logoUrl,
            ],
            'links' => [
                'meetup' => $this->createLink('https://www.meetup.com/fr-FR/', $antenne->meetup->urlName),
                'linkedin' => $this->createLink('https://www.linkedin.com/company/', $antenne->socials->linkedin),
                'bluesky' => $this->createLink('https://bsky.app/profile/', $antenne->socials->bluesky),
            ],
        ];

        $nextMeetup = $this->meetupRepository->findNextForAntenne($antenne);
        if ($nextMeetup) {
            $response['next_meetup'] = [
                'title' => $nextMeetup->getTitle(),
                'date' => $nextMeetup->getDate()->format('Y-m-d H:i:s'),
                'location' => $nextMeetup->getLocation(),
                'description' => $nextMeetup->getDescription(),
                'url' => 'https://www.meetup.com/fr-FR/' . $antenne->meetup->urlName . '/events/' . $nextMeetup->getId(),
            ];
        }

        return new JsonResponse($response);
    }

    private function createLink(string $prefix, ?string $suffix): ?string
    {
        if ($suffix === null) {
            return null;
        }

        return $prefix . $suffix;
    }
}
