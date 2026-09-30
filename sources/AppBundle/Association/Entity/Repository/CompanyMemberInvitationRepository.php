<?php

declare(strict_types=1);

namespace AppBundle\Association\Entity\Repository;

use AppBundle\Association\Entity\CompanyMemberInvitation;
use AppBundle\Association\Enum\InvitationEtat;
use AppBundle\Doctrine\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<CompanyMemberInvitation>
 */
final class CompanyMemberInvitationRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompanyMemberInvitation::class);
    }

    /**
     * @return array<CompanyMemberInvitation>
     */
    public function loadPendingInvitationsByCompany(int $companyId): array
    {
        return ($queryBuilder = $this->createQueryBuilder('i'))
            ->where($queryBuilder->expr()->eq('i.companyId', ':companyId'))
            ->setParameter('companyId', $companyId)
            ->andWhere($queryBuilder->expr()->eq('i.status', ':status'))
            ->setParameter('status', InvitationEtat::EnAttente)
            ->orderBy('i.submittedOn', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findPendingByEmail(int $companyId, string $email): ?CompanyMemberInvitation
    {
        return $this->findOneBy([
            'companyId' => $companyId,
            'email' => $email,
            'status' => InvitationEtat::EnAttente,
        ]);
    }
}
