<?php

declare(strict_types=1);

namespace AppBundle\Event\Model\Repository;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Invoice;
use AppBundle\Event\Model\JoinHydrator;
use AppBundle\Event\Model\Ticket;
use Aura\SqlQuery\Mysql\Select;
use CCMBenchmark\Ting\Driver\Exception;
use CCMBenchmark\Ting\Driver\Mysqli\Serializer\Boolean;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

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

    /**
     * @param $reference
     * @return Invoice
     */
    public function getByReference($reference)
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
