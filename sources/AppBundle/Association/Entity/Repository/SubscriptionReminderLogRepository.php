<?php

declare(strict_types=1);

namespace AppBundle\Association\Entity\Repository;

use AppBundle\Association\Entity\SubscriptionReminderLog;
use AppBundle\Association\Entity\SubscriptionReminderLogEntry;
use AppBundle\Doctrine\EntityRepository;
use CuyZ\Valinor\Mapper\Configurator\ConvertKeysToCamelCase;
use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\MapperBuilder;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<SubscriptionReminderLog>
 */
class SubscriptionReminderLogRepository extends EntityRepository
{
    private const int DEFAULT_LIMIT = 50;

    public function __construct(
        ManagerRegistry $registry,
        private readonly MapperBuilder $mapperBuilder,
    ) {
        parent::__construct($registry, SubscriptionReminderLog::class);
    }

    /**
     * @param int $limit Nombre d'éléments de la page ; un élément supplémentaire
     *                   est remonté pour savoir s'il existe une page suivante
     * @return list<SubscriptionReminderLogEntry>
     */
    public function getPaginatedLogs(int $page = 1, int $limit = self::DEFAULT_LIMIT): array
    {
        $rows = $this->connection()
            ->createQueryBuilder()
            ->select(
                'asrl.id',
                'asrl.reminder_key',
                'asrl.reminder_date',
                'asrl.mail_sent',
                'app.id AS app_id',
                'app.nom',
                'app.prenom',
                'apm.id AS apm_id',
                'apm.raison_sociale',
            )
            ->from('afup_subscription_reminder_log', 'asrl')
            ->leftJoin('asrl', 'afup_personnes_physiques', 'app', 'app.id = asrl.user_id')
            ->leftJoin('app', 'afup_personnes_morales', 'apm', 'apm.id = app.id_personne_morale')
            ->orderBy('asrl.reminder_date', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit + 1)
            ->executeQuery()
            ->fetchAllAssociative()
        ;

        $entries = [];
        foreach ($rows as $row) {
            $entries[] = $this->mapperBuilder
                // mail_sent est un tinyint en base : Valinor exige
                // l'autorisation de cast pour mapper un int vers un bool.
                ->allowScalarValueCasting()
                ->supportDateFormats('Y-m-d H:i:s')
                ->configureWith(new ConvertKeysToCamelCase())
                ->mapper()
                ->map(SubscriptionReminderLogEntry::class, Source::array($row));
        }

        return $entries;
    }

    private function connection(): Connection
    {
        return $this->getEntityManager()->getConnection();
    }
}
