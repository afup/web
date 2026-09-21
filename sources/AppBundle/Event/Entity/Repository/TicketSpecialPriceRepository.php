<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Association\Entity\Utilisateur;
use AppBundle\Event\Entity\Inscription;
use AppBundle\Event\Entity\TicketSpecialPrice;
use AppBundle\Event\Model\Ticket;
use DateTimeImmutable;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<TicketSpecialPrice>
 */
final class TicketSpecialPriceRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TicketSpecialPrice::class);
    }

    /**
     * Retourne le token de prix spécial s'il est dans sa fenêtre de validité
     * et qu'aucune inscription (hors annulations) ne l'a déjà consommé.
     */
    public function findUnusedToken(int $eventId, ?string $token): ?TicketSpecialPrice
    {
        if (!is_string($token) || $token === '') {
            return null;
        }

        $now = new DateTimeImmutable();

        /** @var TicketSpecialPrice|null $ticketSpecialPrice */
        $ticketSpecialPrice = $this->createQueryBuilder('specialPrice')
            ->where('specialPrice.token = :token')
            ->andWhere('specialPrice.eventId = :eventId')
            ->andWhere('specialPrice.dateStart <= :now')
            ->andWhere('specialPrice.dateEnd >= :now')
            ->andWhere('NOT EXISTS (
                SELECT inscription.id
                FROM ' . Inscription::class . ' inscription
                WHERE inscription.specialPriceToken = specialPrice.token
                  AND inscription.etat <> :etatAnnule
            )')
            ->setMaxResults(1)
            ->setParameter('token', $token)
            ->setParameter('eventId', $eventId)
            ->setParameter('now', $now)
            ->setParameter('etatAnnule', Ticket::STATUS_CANCELLED)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $ticketSpecialPrice;
    }

    /**
     * Liste des tokens d'un évènement avec le nom du créateur et l'indicateur
     * d'utilisation (une inscription non annulée a consommé le token).
     *
     * @return list<array{specialPrice: TicketSpecialPrice, creatorPrenom: ?string, creatorNom: ?string, used: bool}>
     */
    public function getByEvent(int $eventId): array
    {
        /** @var list<TicketSpecialPrice> $specialPrices */
        $specialPrices = $this->findBy(['eventId' => $eventId], ['id' => 'DESC']);
        if ($specialPrices === []) {
            return [];
        }

        // Comptage des inscriptions (hors annulées) qui ont consommé chaque token
        $tokens = array_map(
            static fn(TicketSpecialPrice $specialPrice): string => $specialPrice->token,
            $specialPrices,
        );

        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select('inscription.specialPriceToken AS token', 'COUNT(inscription.id) AS nbInscriptions')
            ->from(Inscription::class, 'inscription')
            ->where('inscription.specialPriceToken IN (:tokens)')
            ->andWhere('inscription.etat <> :etatAnnule')
            ->groupBy('inscription.specialPriceToken')
            ->setParameter('tokens', $tokens, ArrayParameterType::STRING)
            ->setParameter('etatAnnule', Ticket::STATUS_CANCELLED)
        ;

        $usedCounts = [];
        foreach ($queryBuilder->getQuery()->getArrayResult() as $row) {
            if (!is_array($row) || !is_string($row['token'] ?? null)) {
                continue; // le groupe par token garantit la présence de la clé
            }

            $token = $row['token'];
            $nbInscriptions = $row['nbInscriptions'] ?? null;
            if (is_int($nbInscriptions)) {
                $usedCounts[$token] = $nbInscriptions > 0;
            }
        }

        // Noms des créateurs, chargés depuis l'entité Utilisateur (table afup_personnes_physiques)
        $creatorIds = array_map(
            static fn(TicketSpecialPrice $specialPrice): int => $specialPrice->creatorId,
            $specialPrices,
        );
        $creators = $this->getEntityManager()
            ->getRepository(Utilisateur::class)
            ->findBy(['id' => array_values(array_unique($creatorIds))])
        ;
        $creatorsById = [];
        foreach ($creators as $creator) {
            if ($creator->id !== null) {
                $creatorsById[$creator->id] = $creator;
            }
        }

        $byEvent = [];
        foreach ($specialPrices as $specialPrice) {
            $creator = $creatorsById[$specialPrice->creatorId] ?? null;
            $byEvent[] = [
                'specialPrice' => $specialPrice,
                'creatorPrenom' => $creator?->firstname,
                'creatorNom' => $creator?->lastname,
                'used' => $usedCounts[$specialPrice->token] ?? false,
            ];
        }

        return $byEvent;
    }
}
