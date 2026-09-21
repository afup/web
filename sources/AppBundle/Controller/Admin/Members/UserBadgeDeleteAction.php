<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members;

use AppBundle\Event\Entity\Repository\BadgeRepository;
use AppBundle\Event\Entity\Repository\UserBadgeRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserBadgeDeleteAction
{
    public function __construct(
        private readonly BadgeRepository $badgeRepository,
        private readonly UserBadgeRepository $userBadgeRepository,
    ) {}

    public function __invoke(Request $request): RedirectResponse
    {
        $badgeId = (int) $request->attributes->get('badgeId');
        $badge = $this->badgeRepository->find($badgeId);
        if (null === $badge) {
            throw new NotFoundHttpException(sprintf('Badge %d inexistant', $badgeId));
        }

        $userBadge = $this->userBadgeRepository->findOneBy([
            'userId' => (int) $request->attributes->get('userId'),
            'badge' => $badge,
        ]);
        if (null === $userBadge) {
            throw new NotFoundHttpException('Attribution de badge introuvable');
        }
        $this->userBadgeRepository->delete($userBadge);

        return new RedirectResponse($request->headers->get('referer'));
    }
}
