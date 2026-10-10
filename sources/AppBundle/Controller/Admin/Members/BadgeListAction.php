<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members;

use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Event\Model\Repository\BadgeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_MEMBRES_READER')]
class BadgeListAction
{
    public function __construct(
        private readonly BadgeRepository $badgeRepository,
        private readonly UserRepository $userRepository,
        private readonly Environment $twig,
    ) {}

    public function __invoke(Request $request): Response
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
