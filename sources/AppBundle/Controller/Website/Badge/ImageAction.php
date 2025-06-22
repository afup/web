<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Badge;

use AppBundle\Event\Model\Repository\BadgeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

final class ImageAction extends AbstractController
{
    public function __construct(
        private readonly BadgeRepository $badgeRepository,
        #[Autowire('%app.badge_dir%')]
        private readonly string $storageDir,
    ) {}

    public function __invoke(int $id): Response
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
