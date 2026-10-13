<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members;

use AppBundle\Event\Entity\Badge;
use AppBundle\Event\Entity\Repository\BadgeRepository;
use AppBundle\Event\Entity\Repository\UserBadgeRepository;
use AppBundle\Event\Entity\UserBadge;
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
        $badgeId = $request->attributes->getInt('badgeId');
        $badge = $this->badgeRepository->find($badgeId);
        if (! $badge instanceof Badge) {
            throw new NotFoundHttpException(sprintf('Badge %d inexistant', $badgeId));
        }

        $userBadge = $this->userBadgeRepository->findOneBy([
            'userId' => $request->attributes->getInt('userId'),
            'badge' => $badge,
        ]);
        if (! $userBadge instanceof UserBadge) {
            throw new NotFoundHttpException('Attribution de badge introuvable');
        }
        $this->userBadgeRepository->delete($userBadge);

        return new RedirectResponse($request->headers->get('referer') ?? $request->getRequestUri());
    }
}
