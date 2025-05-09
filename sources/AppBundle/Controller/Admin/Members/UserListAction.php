<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members;

use AppBundle\Antennes\AntennesCollection;
use AppBundle\Association\Model\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class UserListAction
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly AntennesCollection $antennesCollection,
        private readonly Environment $twig,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        // Modification des paramètres de tri en fonction des demandes passées en GET
        $sort = $request->query->get('sort', 'lastname');
        $direction = $request->query->get('direction', 'asc');
        $filter = $request->query->get('filter');
        $needsUpToDateMembership = $request->query->getBoolean('needsUpToDateMembership');
        $onlyDisplayActive = !$request->query->getBoolean('alsoDisplayInactive');

        return new Response($this->twig->render('admin/members/user_list.html.twig', [
            'antennes' => $this->antennesCollection->getAll(),
            'users' => $this->userRepository->search(
                $sort,
                $direction,
                $filter,
                null,
                null,
                $onlyDisplayActive,
                null,
                $needsUpToDateMembership
            ),
            'needsUpToDateMembership' => $needsUpToDateMembership,
            'onlyDisplayActive' => $onlyDisplayActive,
            'filter' => $filter,
            'sort' => $sort,
            'direction' => $direction,
        ]));
    }
}
