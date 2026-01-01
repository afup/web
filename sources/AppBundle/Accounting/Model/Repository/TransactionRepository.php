<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Model\Repository;

use AppBundle\Accounting\Model\Transaction;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\DateTime;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

/**
 * @extends Repository<Transaction>
 */
class TransactionRepository extends Repository implements MetadataInitializer
{
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);

        $metadata->setEntity(Transaction::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('compta');

        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary'       => true,
                'autoincrement' => true,
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'idclef',
                'fieldName' => 'idKey',
                'type' => 'string',
            ])

            ->addField([
                'columnName' => 'idoperation',
                'fieldName' => 'operationId',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'idcategorie',
                'fieldName' => 'categoryId',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'date_ecriture',
                'fieldName' => 'accountingDate',
                'type' => 'date',
                'serializer' => DateTime::class,
                'serializer_options' => [
                    'serialize' => ['format' => 'Y-m-d'],
                    'unserialize' => ['format' => 'Y-m-d', 'unSerializeUseFormat' => true],
                ],
            ])
            ->addField([
                'columnName' => 'numero_operation',
                'fieldName' => 'operationNumber',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'nom_frs',
                'fieldName' => 'vendorName',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'tva_intra',
                'fieldName' => 'tvaIntra',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'tva_zone',
                'fieldName' => 'tvaZone',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'montant',
                'fieldName' => 'amount',
                'type' => 'float',
            ])
            ->addField([
                'columnName' => 'description',
                'fieldName' => 'description',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'comment',
                'fieldName' => 'comment',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'attachment_required',
                'fieldName' => 'attachmentRequired',
                'type' => 'booleab',
                'default' => false,
            ])
            ->addField([
                'columnName' => 'attachment_filename',
                'fieldName' => 'attachmentFilename',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'numero',
                'fieldName' => 'number',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'idmode_regl',
                'fieldName' => 'paymentTypeId',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'date_regl',
                'fieldName' => 'paymentDate',
                'type' => 'date',
                'serializer' => DateTime::class,
                'serializer_options' => [
                    'serialize' => ['format' => 'Y-m-d'],
                    'unserialize' => ['format' => 'Y-m-d', 'unSerializeUseFormat' => true],
                ],
            ])
            ->addField([
                'columnName' => 'obs_regl',
                'fieldName' => 'paymentComment',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'idevenement',
                'fieldName' => 'eventId',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'idcompte',
                'fieldName' => 'accountId',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'montant_ht_soumis_tva_20',
                'fieldName' => 'amountTva20',
                'type' => 'flaot',
            ])
            ->addField([
                'columnName' => 'montant_ht_soumis_tva_10',
                'fieldName' => 'amountTva10',
                'type' => 'flaot',
            ])
            ->addField([
                'columnName' => 'montant_ht_soumis_tva_5_5',
                'fieldName' => 'amountTva5_5',
                'type' => 'flaot',
            ])
            ->addField([
                'columnName' => 'montant_ht_soumis_tva_0',
                'fieldName' => 'amountTva0',
                'type' => 'flaot',
            ])
        ;

        return $metadata;
    }
}
