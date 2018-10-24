<?php

namespace AppBundle\Event\Model\Repository;

use AppBundle\Event\Model\Event;
use CCMBenchmark\Ting\Repository\HydratorSingleObject;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class EventRepository extends Repository implements MetadataInitializer
{
    /**
     * @return Event|null
     */
    public function getNextEvent()
    {
        $query = $this
            ->getQuery('SELECT id, path, titre, date_fin_appel_conferencier FROM afup_forum WHERE date_debut > NOW() ORDER BY date_debut LIMIT 1')
        ;
        $events = $query->query($this->getCollection(new HydratorSingleObject()));
        if ($events->count() === 0) {
            return null;
        }
        return $events->first();
    }

    /**
     * @return Event|null
     */
    public function getCurrentEvent()
    {
        $query = $this
            ->getQuery('SELECT id, path FROM afup_forum WHERE (date_debut > NOW() OR (NOW() BETWEEN date_debut AND DATE_ADD(date_fin, INTERVAL 1 DAY))) ORDER BY date_debut LIMIT 1')
        ;
        $events = $query->query($this->getCollection(new HydratorSingleObject()));
        if ($events->count() === 0) {
            return null;
        }
        return $events->first();
    }

    /**
     * @param int $eventCount
     *
     * @return \CCMBenchmark\Ting\Repository\CollectionInterface
     */
    public function getPreviousEvents($eventCount)
    {
        $query = $this->getQuery('SELECT * FROM afup_forum WHERE date_debut < NOW() ORDER BY date_debut DESC LIMIT :limit');
        $query->setParams(['limit' => $eventCount]);

        return $query->query($this->getCollection(new HydratorSingleObject()));
    }

    /**
     * @param $path
     *
     * @return Event|null
     */
    public function getByPath($path)
    {
        return $this->getBy(['path' => $path])->first();
    }

    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);
        $metadata->setEntity(Event::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_forum');

        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary'       => true,
                'autoincrement' => true,
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'titre',
                'fieldName' => 'title',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'nb_places',
                'fieldName' => 'seats',
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'date_debut',
                'fieldName' => 'dateStart',
                'type' => 'datetime',
                'serializer_options' => [
                    'unserialize' => ['unSerializeUseFormat' => false]
                ]

            ])
            ->addField([
                'columnName' => 'date_fin',
                'fieldName' => 'dateEnd',
                'type' => 'datetime',
                'serializer_options' => [
                    'unserialize' => ['unSerializeUseFormat' => false]
                ]
            ])
            ->addField([
                'columnName' => 'date_fin_appel_projet',
                'fieldName' => 'dateEndCallForProjects',
                'type' => 'datetime',
                'serializer_options' => [
                    'unserialize' => ['unSerializeUseFormat' => true, 'format' => 'U']
                ]
            ])
            ->addField([
                'columnName' => 'date_fin_appel_conferencier',
                'fieldName' => 'dateEndCallForPapers',
                'type' => 'datetime',
                'serializer_options' => [
                    'unserialize' => ['unSerializeUseFormat' => true, 'format' => 'U']
                ]
            ])
            ->addField([
                'columnName' => 'date_fin_vote',
                'fieldName' => 'dateEndVote',
                'type' => 'datetime'
            ])
            ->addField([
                'columnName' => 'date_fin_prevente',
                'fieldName' => 'dateEndPreSales',
                'type' => 'datetime',
                'serializer_options' => [
                    'unserialize' => ['unSerializeUseFormat' => true, 'format' => 'U']
                ]
            ])
            ->addField([
                'columnName' => 'date_fin_vente',
                'fieldName' => 'dateEndSales',
                'type' => 'datetime',
                'serializer_options' => [
                    'unserialize' => ['unSerializeUseFormat' => true, 'format' => 'U']
                ]
            ])
            ->addField([
                'columnName' => 'date_fin_saisie_repas_speakers',
                'fieldName' => 'dateEndSpeakersDinerInfosCollection',
                'type' => 'datetime',
                'serializer_options' => [
                    'unserialize' => ['unSerializeUseFormat' => true, 'format' => 'U']
                ]
            ])
            ->addField([
                'columnName' => 'date_annonce_planning',
                'fieldName' => 'datePlanningAnnouncement',
                'type' => 'datetime',
                'serializer_options' => [
                    'unserialize' => ['unSerializeUseFormat' => true, 'format' => 'U']
                ]
            ])
            ->addField([
                'columnName' => 'date_fin_saisie_nuites_hotel',
                'fieldName' => 'dateEndHotelInfosCollection',
                'type' => 'datetime',
                'serializer_options' => [
                    'unserialize' => ['unSerializeUseFormat' => true, 'format' => 'U']
                ]
            ])
            ->addField([
                'columnName' => 'text',
                'fieldName' => 'cfp',
                'type' => 'json',
                'serializer_options' => [
                    'unserialize' => ['assoc' => true]
                ]
            ])
            ->addField([
                'columnName' => 'path',
                'fieldName' => 'path',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'trello_list_id',
                'fieldName' => 'trelloListId',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'logo_url',
                'fieldName' => 'logoUrl',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'place_name',
                'fieldName' => 'placeName',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'place_address',
                'fieldName' => 'placeAddress',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'vote_enabled',
                'fieldName' => 'voteEnabled',
                'type' => 'boolean'
            ])
        ;

        return $metadata;
    }
}
