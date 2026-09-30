<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Event\Entity\BadgeAttribue;
use AppBundle\Event\Entity\UserBadge;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<UserBadge>
 */
final class UserBadgeRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserBadge::class);
    }

    /**
     * Retourne les badges attribués à un membre, triés par date d'attribution.
     *
     * La jointure avec afup_badge se fait en DBAL : cette table est encore
     * portée par l'entité Ting AppBundle\Event\Model\Badge.
     *
     * @return list<BadgeAttribue>
     */
    public function findByUserId(int $userId): array
    {
        $rows = $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('badge.id AS badge_id', 'badge.label AS badge_label', 'user_badge.issued_at AS issued_at')
            ->from('afup_personnes_physiques_badge', 'user_badge')
            ->innerJoin('user_badge', 'afup_badge', 'badge', 'user_badge.badge_id = badge.id')
            ->where('user_badge.afup_personne_physique_id = :user_id')
            ->setParameter('user_id', $userId)
            ->orderBy('user_badge.issued_at')
            ->executeQuery()
            ->fetchAllAssociative()
        ;

        $badgesAttribues = [];
        foreach ($rows as $row) {
            $badgeId = $row['badge_id'];
            $badgeLabel = $row['badge_label'];
            $issuedAt = $row['issued_at'];

            if (!is_int($badgeId) || !is_string($badgeLabel) || !is_string($issuedAt)) {
                throw new \RuntimeException('Données inattendues dans la table afup_personnes_physiques_badge.');
            }

            $badgesAttribues[] = new BadgeAttribue(
                $userId,
                $badgeId,
                $badgeLabel,
                new \DateTimeImmutable($issuedAt),
            );
        }

        return $badgesAttribues;
    }
}
