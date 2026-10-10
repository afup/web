<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin;

use AppBundle\Association\Model\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_SUPER_ADMIN')]
class AdministratorsListAction extends AbstractController
{
    public function __construct(private readonly UserRepository $userRepository) {}

    public function __invoke(): Response
    {
        return $this->render('admin/administrators.html.twig', [
            'administrators' => $this->userRepository->getAdministrators(),
        ]);
    }
}
