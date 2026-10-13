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
        $modules = [
            ['name' => 'membres', 'label' => 'Membres'],
            ['name' => 'veille', 'label' => 'Veille'],
            ['name' => 'site', 'label' => 'Site'],
            ['name' => 'apero', 'label' => 'Super-apéro'],
            ['name' => 'event', 'label' => 'Évènements'],
            ['name' => 'compta', 'label' => 'Trésorerie'],
            ['name' => 'ag', 'label' => 'Assemblée générale'],
            ['name' => 'planete', 'label' => 'Planète'],
            ['name' => 'antennes', 'label' => 'Antennes'],
        ];

        return $this->render('admin/administrators.html.twig', [
            'administrators' => $this->userRepository->getAdministrators(),
            'modules' => $modules,
        ]);
    }
}
