<?php

declare(strict_types=1);

namespace AppBundle\Event\Model\Repository;

use AppBundle\Event\Model\SponsorScan;
use AppBundle\Event\Model\SponsorTicket;
use CCMBenchmark\Ting\Repository\HydratorArray;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class SponsorScanRepository extends Repository implements MetadataInitializer
{
    public function getBySponsorTicket(SponsorTicket $sponsorTicket)
    {
        $sql = 'SELECT afup_forum_sponsor_scan.id,
                       afup_inscription_forum.nom,
                       afup_inscription_forum.prenom,
                       afup_inscription_forum.email,
                       afup_forum_sponsor_scan.created_on
                FROM afup_forum_sponsor_scan
                    INNER JOIN afup_inscription_forum ON afup_inscription_forum.id = afup_forum_sponsor_scan.ticket_id
                        AND afup_forum_sponsor_scan.sponsor_ticket_id = :sponsorTicketId
                        AND afup_forum_sponsor_scan.deleted_on IS NULL
                ORDER BY afup_forum_sponsor_scan.created_on DESC';

        return $this->getPreparedQuery($sql)
            ->setParams(['sponsorTicketId' => $sponsorTicket->getId()])
            ->query($this->getCollection(new HydratorArray()));
    }

    /**
     * @inheritDoc
     */
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);

        $metadata->setEntity(SponsorScan::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_forum_sponsor_scan');

        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary'       => true,
                'autoincrement' => true,
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'sponsor_ticket_id',
                'fieldName' => 'sponsorTicketId',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'ticket_id',
                'fieldName' => 'ticketId',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'created_on',
                'fieldName' => 'createdOn',
                'type' => 'datetime',
            ])
            ->addField([
                'columnName' => 'deleted_on',
                'fieldName' => 'deletedOn',
                'type' => 'datetime',
            ])
        ;

        return $metadata;
    }
}
