<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Model\Repository;

use AppBundle\Accounting\Model\InvoicingDetail;
use Aura\SqlQuery\Common\DeleteInterface;
use CCMBenchmark\Ting\Repository\HydratorArray;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

/**
 * @extends Repository<InvoicingDetail>
 */
class InvoicingDetailRepository extends Repository implements MetadataInitializer
{
    public function getRowsIdsPerInvoicingId(int $invoicingId): array
    {
        $query = $this->getQuery(
            'SELECT id FROM afup_compta_facture_details WHERE idafup_compta_facture = :invoicingId',
        )->setParams(['invoicingId' => $invoicingId]);

        $result = [];
        foreach ($query->query($this->getCollection(new HydratorArray())) as $row) {
            $result[] = $row['id'];
        }

        return $result;
    }

    /**
     * @param int[] $ids
     * @return void
     */
    public function removeRowsPerIds(array $ids): void
    {
        /**
         * @var DeleteInterface $builder
         */
        $builder = $this->getQueryBuilder('delete');
        $builder->from($this->getMetadata()->getTable())
                ->where('id IN (:ids)');

        $this->getQuery($builder)->setParams(['ids' => implode(', ', $ids)])->execute();
    }

    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);

        $metadata->setEntity(InvoicingDetail::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_compta_facture_details');

        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary'       => true,
                'autoincrement' => true,
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'idafup_compta_facture',
                'fieldName' => 'invoicingId',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'ref',
                'fieldName' => 'reference',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'designation',
                'fieldName' => 'designation',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'quantite',
                'fieldName' => 'quantity',
                'type' => 'float',
            ])
            ->addField([
                'columnName' => 'pu',
                'fieldName' => 'unitPrice',
                'type' => 'float',
            ])
            ->addField([
                'columnName' => 'tva',
                'fieldName' => 'tva',
                'type' => 'float',
            ])
        ;

        return $metadata;
    }
}
