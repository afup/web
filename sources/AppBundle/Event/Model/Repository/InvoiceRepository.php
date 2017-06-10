<?php

namespace AppBundle\Event\Model\Repository;

use AppBundle\Event\Model\Invoice;
use AppBundle\Event\Model\Ticket;
use CCMBenchmark\Ting\Driver\Exception;
use CCMBenchmark\Ting\Driver\Mysqli\Serializer\Boolean;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class InvoiceRepository extends Repository implements MetadataInitializer
{
    public function saveWithTickets(Invoice $invoice)
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
                'type' => 'false'
            ])
            ->addField([
                'columnName' => 'date_reglement',
                'fieldName' => 'paymentDate',
                'type' => 'datetime',
                'serializer_options' => [
                    'unserialize' => ['unSerializeUseFormat' => true, 'format' => 'U'],
                    'serialize' => ['format' => 'U'],
                ]
            ])
            ->addField([
                'columnName' => 'date_facture',
                'fieldName' => 'invoiceDate',
                'type' => 'datetime',
                'serializer_options' => [
                    'unserialize' => ['unSerializeUseFormat' => true, 'format' => 'U'],
                    'serialize' => ['format' => 'U'],
                ]
            ])
            ->addField([
                'columnName' => 'montant',
                'fieldName' => 'amount',
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'type_reglement',
                'fieldName' => 'paymentType',
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'informations_reglement',
                'fieldName' => 'paymentInfos',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'email',
                'fieldName' => 'email',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'societe',
                'fieldName' => 'company',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'nom',
                'fieldName' => 'lastname',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'prenom',
                'fieldName' => 'firstname',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'adresse',
                'fieldName' => 'address',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'code_postal',
                'fieldName' => 'zipcode',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'ville',
                'fieldName' => 'city',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'id_pays',
                'fieldName' => 'countryId',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'autorisation',
                'fieldName' => 'authorization',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'transaction',
                'fieldName' => 'transaction',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'etat',
                'fieldName' => 'status',
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'facturation',
                'fieldName' => 'invoice',
                'type' => 'bool',
                'serializer' => Boolean::class
            ])
            ->addField([
                'columnName' => 'id_forum',
                'fieldName' => 'forumId',
                'type' => 'int'
            ])
        ;

        return $metadata;
    }
}
