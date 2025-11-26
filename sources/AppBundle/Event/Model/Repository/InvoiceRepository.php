<?php

declare(strict_types=1);

namespace AppBundle\Event\Model\Repository;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Invoice;
use AppBundle\Event\Model\Ticket;
use AppBundle\Ting\JoinHydrator;
use Aura\SqlQuery\Mysql\Select;
use CCMBenchmark\Ting\Driver\Exception;
use CCMBenchmark\Ting\Driver\Mysqli\Serializer\Boolean;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use CCMBenchmark\Ting\Repository\HydratorArray;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

/**
 * @extends Repository<Invoice>
 */
class InvoiceRepository extends Repository implements MetadataInitializer
{
    public function saveWithTickets(Invoice $invoice): void
    {
        $totalAmount = 0;
        /**
         * @var Ticket[] $tickets
         */
        $tickets = $invoice->getTickets();

        try {
            $this->startTransaction();
            $this->unitOfWork->pushSave($invoice);
            foreach ($tickets as $ticket) {
                if ($ticket->getTicketEventType() === null) {
                    continue;
                }
                $ticket
                    ->setTransportMode(null)
                    ->setTransportDistance(null)
                    ->setReference($invoice->getReference())
                    ->setDate(new \DateTime())
                    ->setAmount($ticket->getTicketEventType()->getPrice())
                    ->setStatus(Ticket::STATUS_CREATED)
                    ->setInvoiceStatus(Ticket::INVOICE_TODO)
                    ->setForumId($invoice->getForumId())
                    ->setComments('<tag>' . implode(';', $ticket->getTags()) . '</tag>')
                ;
                $totalAmount += $ticket->getAmount();
                $this->unitOfWork->pushSave($ticket);
            }
            $invoice->setAmount($totalAmount);
            $this->unitOfWork->process();
            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }

    public function getByReference(mixed $reference): ?Invoice
    {
        return $this->getOneBy(['reference' => $reference]);
    }

    public function getPendingBankwires(Event $event)
    {
        /**
         * @var Select $queryBuilder
         */
        $queryBuilder = $this->getQueryBuilder(self::QUERY_SELECT);
        $queryBuilder
            ->cols([
                'invoices.reference',
                'invoices.montant',
                'invoices.date_reglement',
                'invoices.type_reglement',
                'invoices.informations_reglement',
                'invoices.email',
                'invoices.societe',
                'invoices.nom',
                'invoices.prenom',
                'invoices.adresse',
                'invoices.code_postal',
                'invoices.ville',
                'invoices.id_pays',
                'invoices.autorisation',
                'invoices.transaction',
                'invoices.etat',
                'invoices.facturation',
                'invoices.id_forum',
                'invoices.date_facture',
                'inscriptions.id', 'inscriptions.date', 'inscriptions.reference', 'inscriptions.coupon', 'inscriptions.type_inscription',
                'inscriptions.montant', 'inscriptions.informations_reglement', 'inscriptions.civilite', 'inscriptions.nom', 'inscriptions.prenom',
                'inscriptions.email', 'inscriptions.telephone', 'inscriptions.citer_societe', 'inscriptions.newsletter_afup',
                'inscriptions.commentaires', 'inscriptions.etat', 'inscriptions.facturation', 'inscriptions.id_forum',
                'inscriptions.mail_partenaire', 'inscriptions.presence_day1', 'inscriptions.presence_day2',
            ])
            ->from('afup_facturation_forum AS invoices')
            ->innerJoin('afup_inscription_forum as inscriptions', 'ON inscriptions.reference = invoices.reference')
            ->where('invoices.id_forum = :event_id')
            ->where('invoices.type_reglement = :paymentType')
            ->where('invoices.etat = :state')
            ->orderBy(['invoices.reference'])
        ;


        $hydrator = new JoinHydrator();
        $hydrator->aggregateOn('invoices', 'inscriptions', 'getReference');

        return $this
            ->getPreparedQuery($queryBuilder->getStatement())
            ->setParams([
                'event_id' => $event->getId(),
                'paymentType' => Ticket::PAYMENT_BANKWIRE,
                'state' => Ticket::STATUS_CREATED,
            ])
            ->query($this->getCollection($hydrator))
        ;
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
                'serializer_options' => [
                    'unserialize' => ['unSerializeUseFormat' => true, 'format' => 'U'],
                    'serialize' => ['format' => 'U'],
                ],
            ])
            ->addField([
                'columnName' => 'date_facture',
                'fieldName' => 'invoiceDate',
                'type' => 'datetime',
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
