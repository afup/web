<?php

declare(strict_types=1);

namespace AppBundle\AuditLog;

use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\MapperBuilder;
use Doctrine\DBAL\Connection;
use Psr\Clock\ClockInterface;

final readonly class AuditLogRepository
{
    private const ITEMS_PER_PAGE = 10;

    public function __construct(
        private Connection $connection,
        private MapperBuilder $mapperBuilder,
        private ClockInterface $clock,
    ) {}

    public function save(string $texte, ?int $userId, ?string $route): void
    {
        $query = $this->connection->createQueryBuilder()
            ->insert('afup_logs')
            ->setValue('texte', ':texte')
            ->setParameter('texte', $texte)
            ->setValue('date', ':date')
            ->setParameter('date', $this->clock->now()->getTimestamp())
        ;

        if ($userId) {
            $query->setValue('id_personne_physique', ':userId');
            $query->setParameter('userId', $userId);
        }

        if ($route) {
            $query->setValue('route', ':route');
            $query->setParameter('route', $route);
        }

        $query->executeStatement();
    }

    /**
     * @return array<LogEntry>
     */
    public function paginate(int $page): array
    {
        $query = $this->connection->createQueryBuilder()
            ->select('al.*', 'pe.nom', 'pe.prenom')
            ->from('afup_logs', 'al')
            ->leftJoin('al', 'afup_personnes_physiques', 'pe', 'pe.id = al.id_personne_physique')
            ->orderBy('al.date', 'desc')
            ->setMaxResults(self::ITEMS_PER_PAGE)
            ->setFirstResult(($page - 1) * self::ITEMS_PER_PAGE);

        return $this->mapperBuilder
            ->supportDateFormats('U')
            ->mapper()
            ->map(
                'array<' . LogEntry::class . '>',
                Source::array($query->fetchAllAssociative())->camelCaseKeys(),
            );
    }

    public function countPages(): int
    {
        $total = (int) $this->connection->createQueryBuilder()
            ->select('count(*)')
            ->from('afup_logs')
            ->fetchOne();

        $pages = (int) ceil($total / self::ITEMS_PER_PAGE);

        return $pages === 0 ? 1 : $pages;
    }
}
