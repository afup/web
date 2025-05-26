<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website;

use AppBundle\Event\Model\Repository\BadgeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BadgeController extends AbstractController
{
    public function __construct(
        private readonly BadgeRepository $badgeRepository,
        private readonly string $storageDir,
    ) {}

    public function badge($id)
    {
        $badge = $this->badgeRepository->get($id);
        if (null === $badge) {
            throw $this->createNotFoundException();
        }

        $filepath = $this->storageDir . DIRECTORY_SEPARATOR . $badge->getUrl();
        if (false === is_file($filepath)) {
            throw $this->createNotFoundException();
        }

        return new BinaryFileResponse($filepath);
    }
}
