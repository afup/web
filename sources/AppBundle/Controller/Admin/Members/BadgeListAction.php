<?php

namespace AppBundle\Controller\Admin\Members;

use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Event\Model\Repository\BadgeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class BadgeListAction
{
    /** @var BadgeRepository */
    private $badgeRepository;
    /** @var UserRepository */
    private $userRepository;
    /** @var Environment */
    private $twig;

    public function __construct(
        BadgeRepository $badgeRepository,
        UserRepository $userRepository,
        Environment $twig
    ) {
        $this->badgeRepository = $badgeRepository;
        $this->userRepository = $userRepository;
        $this->twig = $twig;
    }

    public function __invoke(Request $request)
    {
        $infos = [];
        foreach ($this->badgeRepository->getAll() as $badge) {
            $infos[] = [
                'badge' => $badge,
                'users' => $this->userRepository->loadByBadge($badge),
            ];
        }

        return new Response($this->twig->render('admin/members/badges/index.html.twig', [
            'title' => 'Badges',
            'infos' => $infos,
        ]));
    }
}
