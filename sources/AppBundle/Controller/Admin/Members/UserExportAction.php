<?php

namespace AppBundle\Controller\Admin\Members;

use AppBundle\Association\Model\Repository\UserRepository;
use SplFileObject;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class UserExportAction
{
    /** @var UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke(Request $request)
    {
        $isActive = $request->query->getBoolean('isActive');
        $isCompanyManager = $request->query->getBoolean('isCompanyManager');
        $filename = tempnam(sys_get_temp_dir(), 'export_personnes_physiques_');
        $file = new SplFileObject($filename, 'w');
        $users = $this->userRepository->search('lastname', 'asc', null, null, null, $isActive, $isCompanyManager);
        foreach ($users as $user) {
            $file->fputcsv([
                $user->getEmail(),
                $user->getLastName(),
                $user->getFirstName(),
            ]);
        }
        $response = new BinaryFileResponse($file);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'export_personnes_physiques.csv');

        return $response;
    }
}
