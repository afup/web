<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Audit;

use AppBundle\AuditLog\Audit;
use AppBundle\AuditLog\AuditLogRepository;
use Symfony\Component\HttpFoundation\Response;

final class IndexAction
{
    public function __construct(
        private readonly AuditLogRepository $auditLogRepository,
        private readonly Audit $audit,
    ) {
    }

    public function __invoke()
    {
        $this->audit->log("Test");

        dump($this->auditLogRepository->paginate(1));

        return new Response();
    }
}
