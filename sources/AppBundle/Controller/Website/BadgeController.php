<?php

namespace AppBundle\Controller\Website;

use AppBundle\Event\Model\Repository\BadgeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BadgeController extends Controller
{
    public function badgeAction($id)
    {
        $badge = $this->get('ting')->get(BadgeRepository::class)->get($id);

        if (null === $badge) {
            throw $this->createNotFoundException();
        }

        $dir = $this->getParameter('kernel.project_dir') . '/htdocs/uploads/badges';

        $filepath = $dir . DIRECTORY_SEPARATOR . $badge->getUrl();

        if (false === is_file($filepath)) {
            throw $this->createNotFoundException();
        }

        return new BinaryFileResponse($filepath);
    }
}
