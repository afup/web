<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\TechLetter;

use AppBundle\TechLetter\DataExtractor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class RetrieveDataAction extends AbstractController
{
    public function __invoke(Request $request): JsonResponse
    {
        $url = $request->request->get('url');
        if ($url === null) {
            throw new BadRequestHttpException('Undefined url parameter');
        }

        $dataExtractor = new DataExtractor();
        $data = $dataExtractor->extractDataForTechLetter($url);

        return new JsonResponse($data);
    }
}
