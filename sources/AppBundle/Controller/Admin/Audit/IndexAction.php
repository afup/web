<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Audit;

use AppBundle\AuditLog\AuditLogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_SUPER_ADMIN')]
final class IndexAction extends AbstractController
{
    public function __construct(private readonly AuditLogRepository $auditLogRepository) {}

    public function __invoke(int $page): Response
    {
        return $this->render('admin/logs.html.twig', [
            'logs' => $this->auditLogRepository->paginate($page),
            'nbPages' => $this->auditLogRepository->countPages(),
            'currentPage' => $page,
        ]);
    }
}
