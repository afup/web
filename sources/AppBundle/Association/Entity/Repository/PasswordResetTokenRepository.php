<?php

declare(strict_types=1);

namespace AppBundle\Association\Entity\Repository;

use AppBundle\Association\Entity\PasswordResetToken;
use AppBundle\Doctrine\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<PasswordResetToken>
 */
class PasswordResetTokenRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PasswordResetToken::class);
    }

    /**
     * La date courante est passée en paramètre (et non lue via NOW()) pour rester
     * pilotable par l'horloge mockée en test.
     */
    public function findValidToken(string $tokenHash, \DateTimeImmutable $now): ?PasswordResetToken
    {
        $token = $this->createQueryBuilder('t')
            ->where('t.tokenHash = :tokenHash')
            ->andWhere('t.expiresAt > :now')
            ->setParameter('tokenHash', $tokenHash)
            ->setParameter('now', $now)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $token instanceof PasswordResetToken ? $token : null;
    }

    /**
     * Purge les tokens expirés, qui ne servent plus à rien : sans cela, une
     * demande jamais consommée laisserait sa ligne indéfiniment.
     */
    public function deleteExpired(\DateTimeImmutable $now): void
    {
        $this->createQueryBuilder('t')
            ->delete()
            ->where('t.expiresAt <= :now')
            ->setParameter('now', $now)
            ->getQuery()
            ->execute()
        ;
    }
}
