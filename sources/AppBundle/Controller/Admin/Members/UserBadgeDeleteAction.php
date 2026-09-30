<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members;

use AppBundle\Event\Entity\Repository\UserBadgeRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class UserBadgeDeleteAction
{
    public function __construct(private readonly UserBadgeRepository $userBadgeRepository) {}

    public function __invoke(Request $request): RedirectResponse
    {
        $badgeId = $request->attributes->get('badgeId');
        $userId = $request->attributes->get('userId');

        if (!is_numeric($badgeId) || !is_numeric($userId)) {
            throw new \RuntimeException('Identifiants de badge invalides.');
        }

        $userBadge = $this->userBadgeRepository->find([
            'badgeId' => (int) $badgeId,
            'userId' => (int) $userId,
        ]);
        if ($userBadge !== null) {
            $this->userBadgeRepository->delete($userBadge);
        }

        return new RedirectResponse($request->headers->get('referer'));
    }
}
