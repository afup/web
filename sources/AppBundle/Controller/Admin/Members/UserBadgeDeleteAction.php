<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members;

use AppBundle\Event\Model\Repository\UserBadgeRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class UserBadgeDeleteAction
{
    public function __construct(private readonly UserBadgeRepository $userBadgeRepository)
    {
    }

    public function __invoke(Request $request): RedirectResponse
    {
        $userBadge = $this->userBadgeRepository->getOneBy([
            'badgeId' => $request->attributes->get('badgeId'),
            'userId' => $request->attributes->get('userId'),
        ]);
        $this->userBadgeRepository->delete($userBadge);

        return new RedirectResponse($request->headers->get('referer'));
    }
}
