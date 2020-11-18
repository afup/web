<?php

namespace App\RendezVous\Infrastructure;

use App\RendezVous\RendezVous;
use App\RendezVous\RendezVousAttendee;
use App\RendezVous\RendezVousAttendeeRepository;
use App\Ting\TingHelper;
use Assert\Assertion;
use Aura\SqlQuery\Common\SelectInterface;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

/**
 * @method RendezVousAttendee|null get($primariesKeyValue, $forceMaster = false)
 */
class TingRendezVousAttendeeRepository extends Repository implements RendezVousAttendeeRepository, MetadataInitializer
{
    public function find($id)
    {
        return $this->get($id);
    }

    /**
     * @return RendezVousAttendee[]&CollectionInterface
     * @phpstan-return \CCMBenchmark\Ting\Repository\CollectionInterface<int, RendezVousAttendee>
     */
    public function findComingUnconfirmed(RendezVous $rendezVous)
    {
        return TingHelper::getResult($this, $this->getQuery(<<<SQL
SELECT * FROM afup_rendezvous_inscrits WHERE id_rendezvous = :id
AND presence = :presence AND confirme IS NULL
SQL
        )
            ->setParams([
                'id' => $rendezVous->getId(),
                'presence' => RendezVousAttendee::COMING,
            ]));
    }

    public function refuseDeclinedInvitations(RendezVous $rendezVous)
    {
        $this->getQuery('UPDATE afup_rendezvous_inscrits SET presence = :presence WHERE confirme = :confirmed AND id_rendezvous = :id')
            ->setParams(['presence' => RendezVousAttendee::REFUSED, 'confirmed' => RendezVousAttendee::DECLINED, 'id' => $rendezVous->getId()])
            ->execute();
    }

    public function fillFreeSpotsWithPending(RendezVous $rendezVous)
    {
        $places = $this->countAvailablePlaces($rendezVous);
        if ($places > 0) {
            $this->getQuery(<<<SQL
UPDATE afup_rendezvous_inscrits SET presence = :newPresence WHERE presence = :oldPresence
AND confirme != :confirmed AND id_rendezvous = :id LIMIT :limit
SQL
            )
                ->setParams([
                    'id' => $rendezVous->getId(),
                    'oldPresence' => RendezVousAttendee::PENDING,
                    'newPresence' => RendezVousAttendee::COMING,
                    'confirmed' => RendezVousAttendee::CONFIRMED,
                    'limit' => $places,
                ])
                ->execute();
        }
    }

    public function findByRendezVous(RendezVous $rendezVous)
    {
        return TingHelper::getResult($this, $this->getQuery('SELECT * FROM afup_rendezvous_inscrits WHERE id_rendezvous = :rendezVousId ORDER BY nom, prenom')
            ->setParams(['rendezVousId' => $rendezVous->getId()]));
    }

    /** @return CollectionInterface&RendezVous[] */
    public function findComingAndPendingByRendezVous(RendezVous $rendezVous)
    {
        return TingHelper::getResult($this, $this->getQuery(<<<'SQL'
SELECT * FROM afup_rendezvous_inscrits 
WHERE id_rendezvous = :rendezVousId AND presence IN (:coming, :pending) 
ORDER BY creation
SQL
        )
            ->setParams(['rendezVousId' => $rendezVous->getId(), 'coming' => RendezVousAttendee::COMING, 'pending' => RendezVousAttendee::PENDING]));
    }

    private function countAvailablePlaces(RendezVous $rendezVous)
    {
        return floor($rendezVous->getCapacity() * RendezVous::COEF_COMING) - $this->countByPresence($rendezVous, RendezVousAttendee::COMING);
    }

    public function countComing(RendezVous $rendezVous)
    {
        return $this->countByPresence($rendezVous, RendezVousAttendee::COMING);
    }

    public function countPending(RendezVous $rendezVous)
    {
        return $this->countByPresence($rendezVous, RendezVousAttendee::PENDING);
    }

    private function countByPresence(RendezVous $rendezVous, $presence = RendezVousAttendee::COMING)
    {
        return (int) $this->getQuery('SELECT COUNT(*) nb FROM afup_rendezvous_inscrits WHERE id_rendezvous = :id AND presence = :presence')
            ->setParams(['id' => $rendezVous->getId(), 'presence' => $presence])
            ->query()->first()[0]->nb;
    }

    /** @return RendezVousAttendee|null */
    public function findOneByHash($hash)
    {
        return TingHelper::getOneOrNullResult($this, $this->getQuery('SELECT * FROM afup_rendezvous_inscrits WHERE MD5(CONCAT(id, id_rendezvous, nom, prenom, email)) = :hash AND presence != :presence')
            ->setParams(['hash' => $hash, 'presence' => RendezVousAttendee::REFUSED]));
    }

    /**
     * @param string      $sort
     * @param string      $direction
     * @param string|null $filter
     *
     * @return RendezVousAttendee[]&CollectionInterface
     * @phpstan-return iterable<RendezVousAttendee>
     */
    public function search(RendezVous $rendezVous, $sort = 'name', $direction = 'asc', $filter = null)
    {
        Assertion::inArray($direction, ['asc', 'desc']);
        $sorts = [
            'name' => ['nom', 'prenom'],
            'company' => ['entreprise'],
            'phone' => ['telephone'],
            'presence' => ['presence'],
            'confirmed' => ['confirme'],
        ];
        Assertion::keyExists($sorts, $sort);
        /** @var SelectInterface $queryBuilder */
        $queryBuilder = $this->getQueryBuilder(self::QUERY_SELECT);
        $queryBuilder
            ->cols(['i.*'])
            ->from('afup_rendezvous_inscrits i')
            ->where('i.id_rendezvous = :id')
            ->bindValue('id', $rendezVous->getId())
            ->orderBy(array_map(static function ($field) use ($direction) {
                return $field.' '.$direction;
            }, $sorts[$sort]));

        if ($filter) {
            $filters = explode(' ', $filter);
            $filters = array_filter(array_map('trim', $filters));
            $ors = [];
            foreach ($filters as $i => $value) {
                $ors[] = "i.nom LIKE :filter$i OR i.prenom LIKE :filter$i OR i.entreprise LIKE :filter$i";
                $queryBuilder->bindValue('filter'.$i, '%'.$value.'%');
            }
            $queryBuilder->where('('.implode(' OR ', $ors).')');
        }

        return TingHelper::getResult($this, $this->getQuery($queryBuilder->getStatement())->setParams($queryBuilder->getBindValues()));
    }

    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);
        $metadata->setEntity(RendezVousAttendee::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_rendezvous_inscrits');
        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary' => true,
                'autoincrement' => true,
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'id_rendezvous',
                'fieldName' => 'rendezVousId',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'nom',
                'fieldName' => 'lastname',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'prenom',
                'fieldName' => 'firstname',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'entreprise',
                'fieldName' => 'company',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'email',
                'fieldName' => 'email',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'telephone',
                'fieldName' => 'phone',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'presence',
                'fieldName' => 'presence',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'confirme',
                'fieldName' => 'confirmed',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'creation',
                'fieldName' => 'creation',
                'type' => 'int',
            ]);

        return $metadata;
    }
}
