<?php

declare(strict_types=1);

namespace AppBundle\Security\ActionThrottling;

use Aura\SqlQuery\Mysql\Select;
use CCMBenchmark\Ting\Repository\HydratorArray;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class LogRepository extends Repository implements MetadataInitializer
{
    /**
     * @param string $action
     * @param string|null $ip
     * @param int|null $objectId
     *
     * @return array
     * @throws \RuntimeException
     */
    public function getApplicableLogs($action, $ip, $objectId, \DateInterval $interval)
    {
        if ($ip === null && $objectId === null) {
            throw new \RuntimeException('I need at least an ip or an object Id to get logs');
        }
        /**
         * @var Select $query
         */
        $query = $this->getQueryBuilder(self::QUERY_SELECT);
        $query
            ->cols(['COUNT(ip) AS ip', 'COUNT(object_id) AS object'])
            ->from('afup_throttling')
            ->where('created_on > :createdOn')
        ;
        $createdOn = new \DateTime();
        $createdOn->sub($interval);

        $params = [
            'createdOn' => $createdOn->format('Y-m-d H:i:s'),
        ];

        $where = [];
        if ($ip !== null) {
            $where[] = 'ip = :ip';
            $params['ip'] = ip2long($ip);
        }
        if ($objectId !== null) {
            $where[] = 'object_id = :id';
            $params['id'] = $objectId;
        }
        $query->where(' (' . implode(' OR ', $where) . ' )');

        return $this
            ->getPreparedQuery($query->getStatement())
            ->setParams($params)
            ->query($this->getCollection(new HydratorArray()))->first()
        ;
    }

    /**
     * @param string $action
     * @param string $ip
     */
    public function removeLogs($action, $ip): void
    {
        $query = $this->getPreparedQuery('
            DELETE FROM afup_throttling
            WHERE `action` = :action
            AND `ip` = :ip
        ');
        $query->setParams([
            'action' => $action,
            'ip' => ip2long($ip),
        ]);

        $query->execute();
    }

    public function clearOldLogs(\DateInterval $delay): void
    {
        $query = $this->getPreparedQuery('
            DELETE FROM afup_throttling
            WHERE `created_on` < :date
        ');
        $date = (new \DateTime())->sub($delay);
        $query->setParams([
            'date' => $date->format('Y-m-d H:i:s'),
        ]);

        $query->execute();
    }

    /**
     * @inheritDoc
     */
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);
        $metadata->setEntity(Log::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_throttling');

        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary'       => true,
                'autoincrement' => true,
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'ip',
                'fieldName' => 'ip',
                'type' => 'ip',
            ])
            ->addField([
                'columnName' => 'action',
                'fieldName' => 'action',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'object_id',
                'fieldName' => 'objectId',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'created_on',
                'fieldName' => 'createdOn',
                'type' => 'datetime',
            ])
        ;

        return $metadata;
    }
}
