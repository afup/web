<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members;

use AppBundle\Association\Model\Repository\UserRepository;
use SplFileObject;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class UserExportAction
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    public function __invoke(Request $request): BinaryFileResponse
    {
        $isActive = $request->query->getBoolean('isActive');
        $isCompanyManager = $request->query->getBoolean('isCompanyManager');
        $baseName = 'export_personnes_physiques';
        if ($isActive) {
            $baseName .= '_actives';
        }
        if ($isCompanyManager) {
            $baseName .= '_managers';
        }
        $tmpFile = tempnam(sys_get_temp_dir(), $baseName);
        $file = new SplFileObject($tmpFile, 'w');
        $users = $this->userRepository->search('lastname', 'asc', null, null, null, $isActive, $isCompanyManager);
        foreach ($users as $user) {
            $file->fputcsv([
                $user->getEmail(),
                $user->getLastName(),
                $user->getFirstName(),
            ]);
        }
        $response = new BinaryFileResponse($file);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $baseName . '.csv');

        return $response;
    }
}
