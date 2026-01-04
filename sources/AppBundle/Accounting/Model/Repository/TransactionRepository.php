<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Model\Repository;

use AppBundle\Accounting\Model\Transaction;
use AppBundle\Accounting\Model\InvoicingPeriod;
use CCMBenchmark\Ting\Repository\HydratorArray;
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
    public function getEntriesPerInvoicingPeriod(InvoicingPeriod $period, bool $onlyUnclasifedEntries, int $operationType = 0)
    {

        $filtre = $operationType === 1 || $operationType === 2 ? 'AND compta.idoperation =\'' . $operationType . '\'  ' : "";

        $requete = 'SELECT ';
        $requete .= 'compta.date_ecriture, compta.description, compta.montant, compta.idoperation,compta.id as idtmp, ';
        $requete .= 'compta.comment, compta.attachment_required, compta.attachment_filename, ';
        $requete .= 'compta_reglement.reglement, ';
        $requete .= 'compta_evenement.evenement, ';
        $requete .= 'compta_categorie.categorie, ';
        $requete .= 'compta_compte.nom_compte,    ';
        $requete .= '(COALESCE(compta.montant_ht_soumis_tva_0,0) + COALESCE(compta.montant_ht_soumis_tva_5_5,0) + COALESCE(compta.montant_ht_soumis_tva_10, 0) + COALESCE(compta.montant_ht_soumis_tva_20, 0)) as montant_ht,   ';
        $requete .= '((COALESCE(compta.montant_ht_soumis_tva_5_5, 0)*0.055) + (COALESCE(compta.montant_ht_soumis_tva_10, 0)*0.1) + (COALESCE(compta.montant_ht_soumis_tva_20, 0)*0.2)) as montant_tva,   ';
        $requete .= 'compta.montant_ht_soumis_tva_0 as montant_ht_0,   ';
        $requete .= 'compta.montant_ht_soumis_tva_5_5 as montant_ht_5_5,   ';
        $requete .= 'compta.montant_ht_soumis_tva_5_5*0.055 as montant_tva_5_5,   ';
        $requete .= 'compta.montant_ht_soumis_tva_10 as montant_ht_10,   ';
        $requete .= 'compta.montant_ht_soumis_tva_10*0.1 as montant_tva_10,   ';
        $requete .= 'compta.montant_ht_soumis_tva_20 as montant_ht_20,   ';
        $requete .= 'compta.montant_ht_soumis_tva_20*0.2 as montant_tva_20,   ';
        $requete .= 'compta.tva_zone   ';
        $requete .= 'FROM ';
        $requete .= 'compta ';
        $requete .= 'LEFT JOIN ';
        $requete .= 'compta_categorie on compta_categorie.id=compta.idcategorie ';
        $requete .= 'LEFT JOIN ';
        $requete .= 'compta_reglement on compta_reglement.id=compta.idmode_regl ';
        $requete .= 'LEFT JOIN ';
        $requete .= 'compta_evenement on compta_evenement.id=compta.idevenement ';
        $requete .= 'LEFT JOIN ';
        $requete .= 'compta_compte on compta_compte.id=compta.idcompte ';
        $requete .= 'WHERE ';
        $requete .= ' compta.date_ecriture >= \'' . $period->getStartDate()->format('Y-m-d') . '\' ';
        $requete .= 'AND compta.date_ecriture <= \'' . $period->getEndDate()->format('Y-m-d') . '\'  ';
        $requete .= $filtre;
        if (true === $onlyUnclasifedEntries) {
            $requete .= ' AND (
                  compta_evenement.evenement = "A déterminer"
                OR
                  compta_categorie.categorie = "A déterminer"
                OR
                  compta_reglement.reglement = "A déterminer"
                OR
                  (compta.attachment_required = 1 AND compta.attachment_filename IS NULL)
            ) ';
        }
        $requete .= 'ORDER BY ';
        $requete .= 'compta.date_ecriture, numero_operation';

        $query = $this->getQuery($requete);

        return $query->query($this->getCollection(new HydratorArray()));
    }

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
