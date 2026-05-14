<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Model\Repository;

use AppBundle\Accounting\InvoicingPaymentStatus;
use CCMBenchmark\Ting\Repository\Hydrator\AggregateFrom;
use CCMBenchmark\Ting\Repository\Hydrator\AggregateTo;
use CCMBenchmark\Ting\Repository\Hydrator\RelationMany;
use CCMBenchmark\Ting\Repository\HydratorRelational;
use CCMBenchmark\Ting\Serializer\DateTime;
use AppBundle\Accounting\InvoicingCurrency;
use AppBundle\Accounting\Model\Invoicing;
use Aura\SqlQuery\Mysql\Select;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use CCMBenchmark\Ting\Repository\HydratorSingleObject;
use CCMBenchmark\Ting\Repository\HydratorArray;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\BackedEnum;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

/**
 * @extends Repository<Invoicing>
 */
class InvoicingRepository extends Repository implements MetadataInitializer
{
    public function getById(int $id): ?Invoicing
    {
        return $this->findOneWithDetails('acf.id = :id', ['id' => $id]);
    }

    public function getQuotationsByPeriodId(?int $periodId = null, string $sort = 'date', string $direction = 'desc'): CollectionInterface
    {
        $filter = 'acf.date_devis';
        if ($sort === 'client') {
            $filter = 'acf.societe';
        }

        /** @var Select $builder */
        $builder = $this->getQueryBuilder(self::QUERY_SELECT);
        $builder->cols(['*', 'sum(acfd.quantite * acfd.pu) as price'])
                ->from('afup_compta_facture acf')
                ->leftJoin('afup_compta_facture_details acfd', 'acfd.idafup_compta_facture = acf.id')
                ->where('acf.numero_devis != ""');

        if ($periodId !== null && $periodId !== 0) {
            $builder->where('acf.date_devis >= (select date_debut from compta_periode where id = :periodId)')
                    ->where('acf.date_devis <= (select date_fin from compta_periode where id = :periodId)')
                    ->bindValues(['periodId' => $periodId]);
        }
        $builder->groupBy(['acf.id', 'date_devis', 'numero_devis', 'date_facture', 'numero_facture', 'societe', 'adresse', 'code_postal', 'ville', 'id_pays', 'email', 'observation', 'ref_clt1', 'ref_clt2', 'ref_clt3', 'nom', 'prenom', 'tel', 'etat_paiement', 'date_paiement', 'devise_facture'])
                ->orderBy(["$filter $direction"]);

        $hydrator = new HydratorSingleObject();
        $hydrator->mapAliasTo('price', 'acf', 'setPrice');


        return $this->getQuery($builder->getStatement())
                    ->setParams($builder->getBindValues())
                    ->query($this->getCollection($hydrator));
    }

    public function getInvoicesByPeriodId(?int $periodId = null, string $sort = 'date', string $direction = 'desc'): CollectionInterface
    {
        $filter = 'acf.date_facture';
        if ($sort === 'client') {
            $filter = 'acf.societe';
        }

        /** @var Select $builder */
        $builder = $this->getQueryBuilder(self::QUERY_SELECT);
        $builder->cols(['*', 'sum(acfd.quantite * acfd.pu) as price'])
                ->from('afup_compta_facture acf')
                ->leftJoin('afup_compta_facture_details acfd', 'acfd.idafup_compta_facture = acf.id')
                ->where('acf.numero_facture != ""');

        if ($periodId !== null && $periodId !== 0) {
            $builder->where('acf.date_facture >= (select date_debut from compta_periode where id = :periodId)')
                    ->where('acf.date_facture <= (select date_fin from compta_periode where id = :periodId)')
                    ->bindValues(['periodId' => $periodId]);
        }
        $builder->groupBy(['acf.id', 'date_devis', 'numero_devis', 'date_facture', 'numero_facture', 'societe', 'adresse', 'code_postal', 'ville', 'id_pays', 'email', 'observation', 'ref_clt1', 'ref_clt2', 'ref_clt3', 'nom', 'prenom', 'tel', 'etat_paiement', 'date_paiement', 'devise_facture'])
                ->orderBy(["$filter $direction"]);

        $hydrator = new HydratorSingleObject();
        $hydrator->mapAliasTo('price', 'acf', 'setPrice');


        return $this->getQuery($builder->getStatement())
                    ->setParams($builder->getBindValues())
                    ->query($this->getCollection($hydrator));
    }

    public function getOneByInvoiceNumber(string $number): ?Invoicing
    {
        return $this->findOneWithDetails('acf.numero_facture = :number', ['number' => $number]);
    }

    public function getOneByQuotationNumber(string $number): ?Invoicing
    {
        return $this->findOneWithDetails('acf.numero_devis = :number', ['number' => $number]);
    }

    public function getNextInvoiceIndex(int $year): ?int
    {
        $sql = 'SELECT MAX(CAST(SUBSTRING_INDEX(numero_facture, \'-\', -1) AS UNSIGNED)) + 1 AS next_index
                FROM afup_compta_facture
                WHERE LEFT(numero_facture, 4) = :year';

        $rows = iterator_to_array(
            $this->getQuery($sql)->setParams(['year' => (string) $year])->query($this->getCollection(new HydratorArray())),
        );

        return isset($rows[0]['next_index']) ? (int) $rows[0]['next_index'] : null;
    }

    public function getNextQuotationIndex(int $year): ?int
    {
        $sql = 'SELECT MAX(CAST(SUBSTRING_INDEX(numero_devis, \'-\', -1) AS UNSIGNED)) + 1 AS next_index
                FROM afup_compta_facture
                WHERE LEFT(numero_devis, 4) = :year';

        $rows = iterator_to_array(
            $this->getQuery($sql)->setParams(['year' => (string) $year])->query($this->getCollection(new HydratorArray())),
        );

        return isset($rows[0]['next_index']) ? (int) $rows[0]['next_index'] : null;
    }

    public function convertQuotationToInvoice(Invoicing $quotation, string $invoiceNumber): void
    {
        $quotation->setInvoiceNumber($invoiceNumber);
        $quotation->setInvoiceDate(new \DateTime());
        $this->save($quotation);
    }

    private function findOneWithDetails(string $whereClause, array $params): ?Invoicing
    {
        /** @var Select $builder */
        $builder = $this->getQueryBuilder(self::QUERY_SELECT);
        $builder->cols(['acf.*', 'acfd.*'])
                ->from('afup_compta_facture acf')
                ->leftJoin('afup_compta_facture_details acfd', 'acfd.idafup_compta_facture = acf.id')
                ->where($whereClause);

        $hydrator = new HydratorRelational();
        $hydrator->addRelation(new RelationMany(new AggregateFrom('acfd'), new AggregateTo('acf'), 'setDetails'));
        $hydrator->callableFinalizeAggregate(fn(array $row) => $row['acf']);

        $collection = $this->getQuery($builder->getStatement())
                    ->setParams($params)
                    ->query($this->getCollection($hydrator));

        if ($collection->count() === 0) {
            return null;
        }

        /** @var Invoicing $entity */
        $entity = $collection->first();
        $entity->setDetails(array_values($entity->getDetails()));

        return $entity;
    }

    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);

        $metadata->setEntity(Invoicing::class);
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
                'fieldName' => 'quotationDate',
                'type' => 'date',
                'serializer' => DateTime::class,
                'serializer_options' => [
                    'serialize' => ['format' => 'Y-m-d'],
                    'unserialize' => ['format' => 'Y-m-d', 'unSerializeUseFormat' => true],
                ],
            ])
            ->addField([
                'columnName' => 'numero_devis',
                'fieldName' => 'quotationNumber',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'date_facture',
                'fieldName' => 'invoiceDate',
                'type' => 'date',
                'serializer' => DateTime::class,
                'serializer_options' => [
                    'serialize' => ['format' => 'Y-m-d'],
                    'unserialize' => ['format' => 'Y-m-d', 'unSerializeUseFormat' => true],
                ],
            ])
            ->addField([
                'columnName' => 'numero_facture',
                'fieldName' => 'invoiceNumber',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'societe',
                'fieldName' => 'company',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'service',
                'fieldName' => 'service',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'adresse',
                'fieldName' => 'address',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'code_postal',
                'fieldName' => 'zipcode',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'ville',
                'fieldName' => 'city',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'id_pays',
                'fieldName' => 'countryId',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'email',
                'fieldName' => 'email',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'tva_intra',
                'fieldName' => 'tvaIntra',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'observation',
                'fieldName' => 'observation',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'ref_clt1',
                'fieldName' => 'refClt1',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'ref_clt2',
                'fieldName' => 'refClt2',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'ref_clt3',
                'fieldName' => 'refClt3',
                'type' => 'string',
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
                'columnName' => 'tel',
                'fieldName' => 'phone',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'etat_paiement',
                'fieldName' => 'paymentStatus',
                'type' => 'enum',
                'serializer' => BackedEnum::class,
                'serializer_options' => [
                    'unserialize' => ['enum' => InvoicingPaymentStatus::class],
                ],
            ])
            ->addField([
                'columnName' => 'date_paiement',
                'fieldName' => 'paymentDate',
                'type' => 'date',
                'serializer' => DateTime::class,
                'serializer_options' => [
                    'serialize' => ['format' => 'Y-m-d'],
                    'unserialize' => ['format' => 'Y-m-d', 'unSerializeUseFormat' => true],
                ],
            ])
            ->addField([
                'columnName' => 'devise_facture',
                'fieldName' => 'currency',
                'type' => 'enum',
                'serializer' => BackedEnum::class,
                'serializer_options' => [
                    'unserialize' => ['enum' => InvoicingCurrency::class],
                ],
            ])
        ;

        return $metadata;
    }
}
