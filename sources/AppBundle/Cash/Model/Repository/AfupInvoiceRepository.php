<?php

declare(strict_types=1);

namespace AppBundle\Cash\Model\Repository;

use AppBundle\Cash\Model\AfupInvoice;
use AppBundle\Event\Model\Repository\list;
use CCMBenchmark\Ting\Exception;
use CCMBenchmark\Ting\Repository\HydratorArray;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

/**
 * @extends Repository<AfupInvoice>
 */
class AfupInvoiceRepository extends Repository implements MetadataInitializer
{
    /**
     * @return list
     */
    public function getList(): array
    {
        $sql = <<<ENDSQL
SELECT acf.id, acf.date_devis, acf.societe, acf.ville, acf.numero_devis, acf.date_facture, acf.ref_clt1, ROUND(SUM(acfd.pu * acfd.quantite), 2) as prix_total_devis
FROM afup_compta_facture acf
INNER JOIN afup_compta_facture_details acfd ON acf.id = acfd.idafup_compta_facture
GROUP BY acf.id
ORDER BY date_devis desc;
ENDSQL;
        $query = $this->getQuery($sql);

        $results = $query->query($this->getCollection(new HydratorArray()));
        $data = [];

        foreach ($results as $result) {
            $data[] = $result;
        }

        return $data;
    }

    /**
     *
     * @return Metadata
     *
     * @throws Exception
     */
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);
        $metadata->setEntity(AfupInvoice::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_compta_facture');

        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary'       => true,
                'autoincrement' => true,
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'date_devis',
                'fieldName' => 'dateEstimate',
                'type' => 'date',
            ])
            ->addField([
                'columnName' => 'societe',
                'fieldName' => 'enterprise',
                'type' => 'string',
            ])
        ;

        return $metadata;
    }
}
