<?php

namespace AppBundle\Controller\Admin\Members;

use AppBundle\Event\Model\Repository\UserBadgeRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class UserBadgeDeleteAction
{
    /** @var UserBadgeRepository */
    private $userBadgeRepository;

    public function __construct(UserBadgeRepository $userBadgeRepository)
    {
        $this->userBadgeRepository = $userBadgeRepository;
    }

    public function __invoke(Request $request)
    {
        $userBadge = $this->userBadgeRepository->getOneBy([
            'badgeId' => $request->attributes->get('badgeId'),
            'userId' => $request->attributes->get('userId'),
        ]);
        $this->userBadgeRepository->delete($userBadge);

        return new RedirectResponse($request->headers->get('referer'));
    }
}
