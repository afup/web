<?php

declare(strict_types=1);

namespace AppBundle\Event\Model\Repository;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\GithubUser;
use AppBundle\Event\Model\Ticket;
use CCMBenchmark\Ting\Driver\Mysqli\Serializer\Boolean;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use CCMBenchmark\Ting\Repository\HydratorArray;
use CCMBenchmark\Ting\Repository\HydratorSingleObject;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class EventRepository extends Repository implements MetadataInitializer
{
    /**
     * @deprecated TODO: à remplacer par getNextEvents de partout
     *
     * @return Event|null
     */
    public function getNextEvent()
    {
        $events = $this->getNextEvents();
        if ($events->count() === 0) {
            return null;
        }
        return $events->first();
    }

    /**
     * @return CollectionInterface|Event|null
     */
    public function getNextEvents()
    {
        $query = $this
            ->getQuery('SELECT id, path, titre, text, date_debut, date_fin, date_fin_appel_conferencier, date_fin_vente FROM afup_forum WHERE date_debut > NOW() ORDER BY date_debut')
        ;

        $events = $query->query($this->getCollection(new HydratorSingleObject()));

        if ($events->count() === 0) {
            return null;
        }
        return $events;
    }

    public function getLastEvent()
    {
        $query = $this
            ->getQuery('SELECT id, path, titre, text, date_debut, date_fin, date_fin_appel_conferencier, date_fin_vente FROM afup_forum ORDER BY date_debut DESC, id DESC')
        ;

        return $query->query($this->getCollection(new HydratorSingleObject()))->first();
    }

    public function getNextEventForGithubUser(GithubUser $githubUser)
    {
        $events = $this
            ->getPreparedQuery('SELECT id, path, titre, date_debut, date_fin, date_fin_appel_conferencier
                             FROM afup_forum
                             WHERE date_debut > NOW()
                             AND afup_forum.id IN (
                               SELECT afup_sessions.id_forum
                               FROM afup_conferenciers_sessions
                               JOIN afup_sessions ON (afup_conferenciers_sessions.session_id = afup_sessions.session_id)
                               JOIN afup_conferenciers ON (afup_conferenciers_sessions.conferencier_id = afup_conferenciers.conferencier_id)
                               WHERE afup_sessions.plannifie = 1
                               AND afup_conferenciers.user_github = :user_github_id
                               )
                             ORDER BY date_debut LIMIT 1')
            ->setParams(['user_github_id' => $githubUser->getId()])
            ->query($this->getCollection(new HydratorSingleObject()))
        ;

        if ($events->count() === 0) {
            return null;
        }

        return $events->first();
    }

    public function getList($id = null)
    {
        $sql = <<<ENDSQL
SELECT f.id, f.titre, f.path, f.nb_places, f.date_debut, f.date_fin, f.date_fin_appel_conferencier, f.date_fin_vente, f.archived_at
FROM afup_forum f
%s
GROUP BY f.id, f.titre, f.path, f.nb_places, f.date_debut, f.date_fin, f.date_fin_appel_conferencier, f.date_fin_vente
ORDER BY date_debut desc;
ENDSQL;
        $sql = sprintf($sql, $id === null ? '':'WHERE f.id = :id');

        $sessions = $this->countRelation('afup_sessions');
        $inscriptions = $this->countRelation('afup_inscription_forum');

        $query = $this->getQuery($sql);
        if ($id !== null) {
            $query->setParams(['id'=>$id]);
        }

        $results = $query->query($this->getCollection(new HydratorArray()));
        $data = [];

        foreach ($results as $result) {
            $result['est_supprimable'] = !array_key_exists($result['id'], $sessions) && !array_key_exists($result['id'], $inscriptions);

            $data[] = $result;
        }

        return $data;
    }

    /**
     * @return array<int, int>
     */
    private function countRelation(string $table): array
    {
        if (!in_array($table, ['afup_sessions', 'afup_inscription_forum'], true)) {
            throw new \InvalidArgumentException('Table non gérée');
        }

        $query = $this->getQuery('SELECT id_forum, COUNT(id_forum) as quantity FROM ' . $table . ' GROUP BY id_forum;');
        $results = $query->query($this->getCollection(new HydratorArray()));

        $data = [];

        foreach ($results as $result) {
            $data[$result['id_forum']] = $result['quantity'];
        }

        return $data;
    }

    public function getAllPastEventWithSpeakerEmail($email)
    {
        $sql = <<<SQL
SELECT afup_forum.*
FROM afup_forum
JOIN afup_conferenciers ON (afup_forum.id = afup_conferenciers.id_forum)
JOIN afup_conferenciers_sessions ON (afup_conferenciers.conferencier_id = afup_conferenciers_sessions.conferencier_id)
JOIN afup_sessions ON (afup_conferenciers_sessions.session_id = afup_sessions.session_id)
WHERE (afup_sessions.date_publication IS NULL OR afup_sessions.date_publication < NOW())
AND afup_conferenciers.email = :email
AND afup_sessions.plannifie = 1
AND afup_forum.date_fin < NOW()
GROUP BY afup_forum.id
ORDER BY afup_forum.date_debut DESC
SQL;
        $query = $this->getQuery($sql);
        $query->setParams(['email' => $email]);

        return $query->query($this->getCollection(new HydratorSingleObject()));
    }

    public function getAllPastEventWithTegistrationEmail($email)
    {
        $sql = <<<SQL
SELECT afup_forum.*
FROM afup_forum
JOIN afup_inscription_forum ON (afup_forum.id = afup_inscription_forum.id_forum)
WHERE afup_inscription_forum.email = :email
AND (afup_inscription_forum.etat = :status_paid OR afup_inscription_forum.etat = :status_guest)
AND afup_forum.date_fin < NOW()
GROUP BY afup_forum.id
ORDER BY afup_forum.date_debut DESC
SQL;
        $query = $this->getQuery($sql);
        $query->setParams([
            'email' => $email,
            'status_paid' => Ticket::STATUS_PAID,
            'status_guest' => Ticket::STATUS_GUEST,
        ]);

        return $query->query($this->getCollection(new HydratorSingleObject()));
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

    public function getLastYearEvent(Event $event): Event
    {
        // Recherche de l'année dans le nom
        preg_match('#\d{4}#', $event->getTitle(), $matches);
        if (!$matches) {
            return $this->getLastEvent();
        }

        $year = (int) $matches[0];
        $lastYear = $year - 1;
        $searchTitle = str_replace((string) $year, (string) $lastYear, $event->getTitle());

        // Recherche par nom (N-1)
        $lastYearEvent = $this->getBy(['title' => $searchTitle])->first();
        if (!$lastYearEvent) {
            $lastYearEvent = $this->getPreviousEvents(1)->first();
        }

        return $lastYearEvent;
    }

    /**
     * @param int $eventCount
     *
     * @return CollectionInterface
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

    public function getAllActive(): CollectionInterface
    {
        $query = $this->getQuery('SELECT * FROM afup_forum WHERE archived_at IS NULL');

        return $query->query($this->getCollection(new HydratorSingleObject()));
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
                    'unserialize' => ['unSerializeUseFormat' => true, 'format' => 'U'],
                    'serialize' => ['serializeUseFormat' => true, 'format' => 'U'],
                ]
            ])
            ->addField([
                'columnName' => 'date_fin_appel_conferencier',
                'fieldName' => 'dateEndCallForPapers',
                'type' => 'datetime',
                'serializer_options' => [
                    'unserialize' => ['unSerializeUseFormat' => true, 'format' => 'U'],
                    'serialize' => ['serializeUseFormat' => true, 'format' => 'U'],
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
                    'unserialize' => ['unSerializeUseFormat' => true, 'format' => 'U'],
                    'serialize' => ['serializeUseFormat' => true, 'format' => 'U'],
                ]
            ])
            ->addField([
                'columnName' => 'date_fin_vente',
                'fieldName' => 'dateEndSales',
                'type' => 'datetime',
                'serializer_options' => [
                    'unserialize' => ['unSerializeUseFormat' => true, 'format' => 'U'],
                    'serialize' => ['serializeUseFormat' => true, 'format' => 'U'],
                ]
            ])
            ->addField([
                'columnName' => 'date_fin_vente_token_sponsor',
                'fieldName' => 'dateEndSalesSponsorToken',
                'type' => 'datetime',
                'serializer_options' => [
                    'unserialize' => ['unSerializeUseFormat' => true, 'format' => 'U'],
                    'serialize' => ['serializeUseFormat' => true, 'format' => 'U'],
                ]
            ])
            ->addField([
                'columnName' => 'date_fin_saisie_repas_speakers',
                'fieldName' => 'dateEndSpeakersDinerInfosCollection',
                'type' => 'datetime',
                'serializer_options' => [
                    'unserialize' => ['unSerializeUseFormat' => true, 'format' => 'U'],
                    'serialize' => ['serializeUseFormat' => true, 'format' => 'U'],
                ]
            ])
            ->addField([
                'columnName' => 'date_annonce_planning',
                'fieldName' => 'datePlanningAnnouncement',
                'type' => 'datetime',
                'serializer_options' => [
                    'unserialize' => ['unSerializeUseFormat' => true, 'format' => 'U'],
                    'serialize' => ['serializeUseFormat' => true, 'format' => 'U'],
                ]
            ])
            ->addField([
                'columnName' => 'date_fin_saisie_nuites_hotel',
                'fieldName' => 'dateEndHotelInfosCollection',
                'type' => 'datetime',
                'serializer_options' => [
                    'unserialize' => ['unSerializeUseFormat' => true, 'format' => 'U'],
                    'serialize' => ['serializeUseFormat' => true, 'format' => 'U'],
                ]
            ])
            ->addField([
                'columnName' => 'text',
                'fieldName' => 'CFP',
                'type' => 'json',
                'serializer_options' => [
                    'unserialize' => ['assoc' => true],
                ]
            ])
            ->addField([
                'columnName' => 'path',
                'fieldName' => 'path',
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
                'type' => 'bool',
                'serializer' => Boolean::class
            ])
            ->addField([
                'columnName' => 'has_prices_defined_with_vat',
                'fieldName' => 'hasPricesDefinedWithVat',
                'type' => 'bool',
                'serializer' => Boolean::class
            ])
            ->addField([
                'columnName' => 'speakers_diner_enabled',
                'fieldName' => 'speakersDinerEnabled',
                'type' => 'bool',
                'serializer' => Boolean::class
            ])
            ->addField([
                'columnName' => 'accomodation_enabled',
                'fieldName' => 'accomodationEnabled',
                'type' => 'bool',
                'serializer' => Boolean::class
            ])
            ->addField([
                'columnName' => 'waiting_list_url',
                'fieldName' => 'waitingListUrl',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'transport_information_enabled',
                'fieldName' => 'transportInformationEnabled',
                'type' => 'bool',
                'serializer' => Boolean::class
            ])
            ->addField([
                'columnName' => 'archived_at',
                'fieldName' => 'archivedAt',
                'type' => 'datetime',
            ])
        ;

        return $metadata;
    }
}
