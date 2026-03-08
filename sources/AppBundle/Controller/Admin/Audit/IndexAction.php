<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Audit;

use AppBundle\AuditLog\AuditLogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class IndexAction extends AbstractController
{
    public function __construct(
        private readonly AuditLogRepository $auditLogRepository,
    ) {}

    public function __invoke(int $page): Response
    {
        return $this->render('admin/logs.html.twig', [
            'logs' => $this->auditLogRepository->paginate($page),
            'nbPages' => $this->auditLogRepository->countPages(),
            'currentPage' => $page,
        ]);
    }
}
