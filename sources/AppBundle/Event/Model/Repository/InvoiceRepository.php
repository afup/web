<?php

declare(strict_types=1);

namespace AppBundle\Event\Model\Repository;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Invoice;
use AppBundle\Event\Model\TicketAggregate;
use AppBundle\Ting\DateTimeWithTimeZoneSerializer;
use Aura\SqlQuery\Mysql\Select;
use CCMBenchmark\Ting\Driver\Mysqli\Serializer\Boolean;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use CCMBenchmark\Ting\Repository\HydratorArray;
use CCMBenchmark\Ting\Repository\HydratorSingleObject;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;
use AppBundle\Event\Enum\InscriptionEtat;
use AppBundle\Event\Enum\TypeReglement;

/**
 * @extends Repository<Invoice>
 */
class InvoiceRepository extends Repository implements MetadataInitializer
{
    public function getByReference(mixed $reference): ?Invoice
    {
        return $this->getOneBy(['reference' => $reference]);
    }

    public function getWithEventDataByReference(string $reference): ?array
    {
        $query = $this->getQuery(
            'SELECT aff.*, af.titre AS event_name, af.has_prices_defined_with_vat AS event_has_prices_defined_with_vat
            FROM afup_facturation_forum aff
            LEFT JOIN afup_forum af ON af.id = aff.id_forum
            WHERE aff.reference = :reference',
        );
        $query->setParams(['reference' => $reference]);

        return $query->query($this->getCollection(new HydratorArray()))->first();
    }

    /**
     * Factures réglées par virement et en attente de paiement, ayant au moins une inscription.
     *
     * @return CollectionInterface<Invoice>&iterable<Invoice>
     */
    public function getPendingBankwires(Event $event): CollectionInterface
    {
        return $this
            ->getPreparedQuery(
                'SELECT invoices.*
                FROM afup_facturation_forum AS invoices
                WHERE invoices.id_forum = :event_id
                AND invoices.type_reglement = :paymentType
                AND invoices.etat = :state
                AND EXISTS (
                    SELECT 1 FROM afup_inscription_forum AS inscriptions
                    WHERE inscriptions.reference = invoices.reference
                )
                ORDER BY invoices.reference',
            )
            ->setParams([
                'event_id' => $event->getId(),
                'paymentType' => TypeReglement::Virement->value,
                'state' => InscriptionEtat::Creee->value,
            ])
            ->query($this->getCollection(new HydratorSingleObject()))
        ;
    }

    /**
     * @param list<string> $references
     *
     * @return array<string, Invoice>
     */
    public function getIndexedByReferences(array $references): array
    {
        $references = array_values(array_unique($references));
        if ($references === []) {
            return [];
        }

        $params = [];
        foreach ($references as $index => $reference) {
            $params['reference' . $index] = $reference;
        }

        $invoices = $this
            ->getPreparedQuery(sprintf(
                'SELECT aff.* FROM afup_facturation_forum aff WHERE aff.reference IN (%s)',
                implode(', ', array_map(static fn(string $key): string => ':' . $key, array_keys($params))),
            ))
            ->setParams($params)
            ->query($this->getCollection(new HydratorSingleObject()))
        ;

        $indexed = [];
        foreach ($invoices as $invoice) {
            if ($invoice instanceof Invoice) {
                $indexed[$invoice->getReference()] = $invoice;
            }
        }

        return $indexed;
    }

    /**
     * Complète les agrégats d'inscriptions produits par InscriptionRepository avec leur facture.
     *
     * @param array<TicketAggregate> $ticketAggregates
     *
     * @return list<TicketAggregate>
     */
    public function enrichTicketAggregates(array $ticketAggregates): array
    {
        $invoices = $this->getIndexedByReferences(array_values(array_map(
            static fn(TicketAggregate $ticketAggregate): string => $ticketAggregate->ticket->reference,
            $ticketAggregates,
        )));

        $enriched = [];
        foreach ($ticketAggregates as $ticketAggregate) {
            $enriched[] = new TicketAggregate(
                $ticketAggregate->ticket,
                $ticketAggregate->ticketType,
                $invoices[$ticketAggregate->ticket->reference] ?? null,
                $ticketAggregate->lastSubscription,
            );
        }

        return $enriched;
    }

    public function searchAllPastEventsInvoices(string $search): CollectionInterface
    {
        $query = $this->getQuery('SELECT inv.*, forum.titre AS forum_titre
  FROM afup_facturation_forum AS inv
  LEFT JOIN afup_forum AS forum ON inv.id_forum = forum.id
  WHERE
    inv.reference LIKE :like
    OR inv.informations_reglement LIKE :like
    OR inv.email LIKE :like
    OR inv.societe LIKE :like
    OR inv.nom LIKE :like
    OR inv.prenom LIKE :like
    OR inv.autorisation LIKE :like
    OR inv.transaction LIKE :like');
        $query->setParams(['like' => '%' . $search . '%']);

        return $query->query($this->getCollection(new HydratorArray()));
    }

    public function searchAllQuotesAndInvoices(string $search): CollectionInterface
    {
        $query = $this->getQuery("SELECT inv.*, SUM(det.pu * det.quantite) AS total,
    GROUP_CONCAT(det.ref SEPARATOR ', ') AS refs,
    GROUP_CONCAT(det.designation SEPARATOR ', ') AS details
  FROM afup_compta_facture AS inv
  LEFT JOIN afup_compta_facture_details AS det
    ON det.idafup_compta_facture = inv.id AND det.quantite > 0
  WHERE
    inv.numero_devis LIKE :like
    OR inv.numero_facture LIKE :like
    OR inv.societe LIKE :like
    OR inv.service LIKE :like
    OR inv.email LIKE :like
    OR inv.ref_clt1 LIKE :like
    OR inv.ref_clt2 LIKE :like
    OR inv.ref_clt3 LIKE :like
    OR inv.observation LIKE :like
  GROUP BY inv.id");
        $query->setParams(['like' => '%' . $search . '%']);

        return $query->query($this->getCollection(new HydratorArray()));
    }

    public function getByEventId(int $eventId, string $sort = 'date_facture', string $direction = 'desc', string $filter = ''): CollectionInterface
    {
        /** @var Select $builder */
        $builder = $this->getQueryBuilder(self::QUERY_SELECT);
        $builder->cols(['*'])
                ->from('afup_facturation_forum aff')
                ->where('aff.id_forum = :event_id')
                ->where(
                    'aff.etat in (' . InscriptionEtat::Reglee->value . ', ' . InscriptionEtat::AttenteReglement->value . ', ' . InscriptionEtat::Confirmee->value . ')',
                );
        if ($filter !== '') {
            $builder->where('(aff.societe LIKE :filter OR aff.reference LIKE :filter)')
                    ->bindValues(['filter' => '%' . $filter . '%']);
        }
        $builder->orderBy(["$sort $direction"])
                ->bindValues(['event_id' => $eventId]);

        return $this->getQuery($builder->getStatement())
                    ->setParams($builder->getBindValues())
                    ->query($this->getCollection(new HydratorSingleObject()));
    }


    /**
     * @inheritDoc
     */
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);

        $metadata->setEntity(Invoice::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_facturation_forum');

        $metadata
            ->addField([
                'columnName' => 'reference',
                'fieldName' => 'reference',
                'primary'       => true,
                'type' => 'false',
            ])
            ->addField([
                'columnName' => 'date_reglement',
                'fieldName' => 'paymentDate',
                'type' => 'datetime',
                'serializer' => DateTimeWithTimeZoneSerializer::class,
                'serializer_options' => [
                    'unserialize' => ['unSerializeUseFormat' => true, 'format' => 'U'],
                    'serialize' => ['format' => 'U'],
                ],
            ])
            ->addField([
                'columnName' => 'date_facture',
                'fieldName' => 'invoiceDate',
                'type' => 'datetime',
                'serializer' => DateTimeWithTimeZoneSerializer::class,
                'serializer_options' => [
                    'unserialize' => ['unSerializeUseFormat' => true, 'format' => 'U'],
                    'serialize' => ['format' => 'U'],
                ],
            ])
            ->addField([
                'columnName' => 'montant',
                'fieldName' => 'amount',
                'type' => 'float',
            ])
            ->addField([
                'columnName' => 'type_reglement',
                'fieldName' => 'paymentType',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'informations_reglement',
                'fieldName' => 'paymentInfos',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'email',
                'fieldName' => 'email',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'societe',
                'fieldName' => 'company',
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
                'columnName' => 'autorisation',
                'fieldName' => 'authorization',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'transaction',
                'fieldName' => 'transaction',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'etat',
                'fieldName' => 'status',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'facturation',
                'fieldName' => 'invoice',
                'type' => 'bool',
                'serializer' => Boolean::class,
            ])
            ->addField([
                'columnName' => 'id_forum',
                'fieldName' => 'forumId',
                'type' => 'int',
            ])
        ;

        return $metadata;
    }
}
