<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Model\Repository;

use AppBundle\Accounting\Model\InvoicingPeriod;
use CCMBenchmark\Ting\Driver\Mysqli\Serializer\Boolean;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;
use DateTime;

/**
 * @extends Repository<InvoicingPeriod>
 */
class InvoicingPeriodRepository extends Repository implements MetadataInitializer
{
    public function getCurrentPeriod(?int $periodId = null): InvoicingPeriod
    {
        if ($periodId !== null) {
            return $this->get($periodId);
        }

        $startDate = new DateTime(date("Y") . "-01-01");
        $endDate = new DateTime(date("Y") . "-12-31");
        $period = $this->getOneBy([
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
        ]);

        if (!$period instanceof InvoicingPeriod) {
            $period = new InvoicingPeriod();
            $period->setStartDate($startDate);
            $period->setEndDate($endDate);
            $this->save($period);
            $period = $this->getOneBy([
                'startDate' => $startDate->format('Y-m-d'),
                'endDate' => $endDate->format('Y-m-d'),
            ]);
        }

        return $period;
    }

    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);

        $metadata->setEntity(InvoicingPeriod::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('compta_periode');

        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary'       => true,
                'autoincrement' => true,
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'date_debut',
                'fieldName' => 'startDate',
                'type' => 'datetime',
                'serializer' => \CCMBenchmark\Ting\Serializer\DateTime::class,
                'serializer_options' => [
                    'serialize' => ['format' => 'Y-m-d'],
                    'unserialize' => ['format' => 'Y-m-d', 'unSerializeUseFormat' => true],
                ],
            ])
            ->addField([
                'columnName' => 'date_fin',
                'fieldName' => 'endDate',
                'type' => 'datetime',
                'serializer' => \CCMBenchmark\Ting\Serializer\DateTime::class,
                'serializer_options' => [
                    'serialize' => ['format' => 'Y-m-d'],
                    'unserialize' => ['format' => 'Y-m-d', 'unSerializeUseFormat' => true],
                ],
            ])
            ->addField([
                'columnName' => 'verouiller',
                'fieldName' => 'locked',
                'type' => 'bool',
                'serializer' => Boolean::class,
            ])
        ;

        return $metadata;
    }
}
