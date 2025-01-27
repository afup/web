<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website;

use AppBundle\Event\Model\Repository\BadgeRepository;
use CCMBenchmark\TingBundle\Repository\RepositoryFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BadgeController extends AbstractController
{
    private RepositoryFactory $repositoryFactory;
    private string $storageDir;

    public function __construct(RepositoryFactory $repositoryFactory,
                                string $storageDir)
    {
        $this->repositoryFactory = $repositoryFactory;
        $this->storageDir = $storageDir;
    }
    public function badge($id)
    {
        $badge = $this->repositoryFactory->get(BadgeRepository::class)->get($id);
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
