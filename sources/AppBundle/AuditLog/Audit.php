<?php

declare(strict_types=1);

namespace AppBundle\AuditLog;

use AppBundle\Association\Model\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

final readonly class Audit
{
    public function __construct(
        private Security $security,
        private RequestStack $requestStack,
        private AuditLogRepository $repository,
    ) {}

    public function log(string $message): void
    {
        $userId = null;
        $user = $this->security->getUser();
        if ($user instanceof User) {
            $userId = $user->getId();
        }

        $route = $this->requestStack->getCurrentRequest()->get('_route');

        $this->repository->save($message, $userId, $route);
    }
}
