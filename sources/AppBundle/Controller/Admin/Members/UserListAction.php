<?php

namespace AppBundle\Controller\Admin\Members;

use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Offices\OfficesCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class UserListAction
{
    /** @var UserRepository */
    private $userRepository;
    /** @var OfficesCollection */
    private $officesCollection;
    /** @var Environment */
    private $twig;

    public function __construct(
        UserRepository $userRepository,
        OfficesCollection $officesCollection,
        Environment $twig
    ) {
        $this->userRepository = $userRepository;
        $this->officesCollection = $officesCollection;
        $this->twig = $twig;
    }

    public function __invoke(Request $request)
    {
        // Modification des paramètres de tri en fonction des demandes passées en GET
        $sort = $request->query->get('sort', 'lastname');
        $direction = $request->query->get('direction', 'asc');
        $filter = $request->query->get('filter');
        $needsUpToDateMembership = $request->query->getBoolean('needsUpToDateMembership');
        $onlyDisplayActive = !$request->query->getBoolean('alsoDisplayInactive');

        return new Response($this->twig->render('admin/members/user_list.html.twig', [
            'offices' => $this->officesCollection->getAll(),
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
