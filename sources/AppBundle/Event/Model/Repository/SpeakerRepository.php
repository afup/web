<?php

declare(strict_types=1);

namespace AppBundle\Event\Model\Repository;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\JoinHydrator;
use AppBundle\Event\Model\Speaker;
use AppBundle\Event\Model\Talk;
use Assert\Assertion;
use CCMBenchmark\Ting\Driver\Mysqli\Serializer\Boolean;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use CCMBenchmark\Ting\Repository\HydratorSingleObject;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

/**
 * @extends Repository<Speaker>
 */
class SpeakerRepository extends Repository implements MetadataInitializer
{
    /**
     * @return CollectionInterface<Speaker>&iterable<Speaker>
     */
    public function getSpeakersByTalk(Talk $talk)
    {
        $query = $this->getPreparedQuery('SELECT c.conferencier_id, c.id_forum, c.civilite, c.nom, c.prenom, c.email,c.societe,
        c.biographie, c.twitter, c.user_github, c.photo, c.bluesky, c.mastodon
        FROM afup_conferenciers c
        LEFT JOIN afup_conferenciers_sessions cs ON cs.conferencier_id = c.conferencier_id
        WHERE cs.session_id = :talkId
        ')->setParams(['talkId' => $talk->getId()]);

        return $query->query($this->getCollection(new HydratorSingleObject()));
    }

    /**
     * Retrieve speakers with a scheduled talk for a given event
     * @param bool $returnTalksThatWillBePublished
     * @return CollectionInterface
     */
    public function getScheduledSpeakersByEvent(Event $event, $returnTalksThatWillBePublished = false)
    {
        $hydrator = new JoinHydrator();
        $hydrator->aggregateOn('speaker', 'talk', 'getId');

        $publishedAtFilter = '(talk.date_publication < NOW() OR talk.date_publication IS NULL)';
        if ($returnTalksThatWillBePublished) {
            $publishedAtFilter = '(1 = 1)';
        }

        $query = $this->getPreparedQuery('SELECT speaker.conferencier_id, speaker.id_forum, speaker.civilite, speaker.nom, speaker.prenom, speaker.email, speaker.societe,
        speaker.biographie, speaker.twitter, speaker.mastodon, speaker.bluesky, speaker.user_github, speaker.photo, talk.titre, talk.session_id,
        speaker.will_attend_speakers_diner,
        speaker.has_special_diet,
        speaker.referent_person,
        speaker.referent_person_email,
        speaker.special_diet_description,
        speaker.hotel_nights,
        speaker.phone_number
        FROM afup_conferenciers speaker
        INNER JOIN afup_conferenciers_sessions cs ON cs.conferencier_id = speaker.conferencier_id
        INNER JOIN afup_sessions talk ON talk.session_id = cs.session_id
        WHERE speaker.id_forum = :event AND talk.plannifie=1 AND ' . $publishedAtFilter . '
        ORDER BY speaker.prenom ASC, speaker.nom ASC
        ')->setParams(['event' => $event->getId()]);

        return $query->query($this->getCollection($hydrator));
    }

    /**
     * @return CollectionInterface
     */
    public function getSpeakersByEvent(Event $event)
    {
        $query = $this->getPreparedQuery(
            'SELECT afup_conferenciers.*
        FROM afup_conferenciers
        JOIN afup_conferenciers_sessions ON (afup_conferenciers_sessions.conferencier_id = afup_conferenciers.conferencier_id)
        JOIN afup_sessions ON (afup_conferenciers_sessions.session_id = afup_sessions.session_id)
        JOIN afup_forum_planning ON (afup_forum_planning.id_session = afup_sessions.session_id)
        WHERE afup_sessions.id_forum = :eventId
        GROUP BY afup_conferenciers.conferencier_id
        '
        )->setParams(['eventId' => $event->getId()]);

        return $query->query($this->getCollection(new HydratorSingleObject()));
    }

    public function getFromLastEventAndUserId($eventId, $githubUserId)
    {
        $query = $this->getPreparedQuery(
            'SELECT afup_conferenciers.*
        FROM afup_conferenciers
        JOIN afup_forum ON (afup_forum.id = afup_conferenciers.id_forum)
        WHERE afup_conferenciers.id_forum != :eventId
        AND afup_conferenciers.user_github = :userGithub
        GROUP BY afup_conferenciers.conferencier_id, afup_forum.date_debut
        ORDER BY afup_forum.date_debut DESC
        LIMIT 1
        '
        )->setParams(['eventId' => $eventId, 'userGithub' => $githubUserId]);

        $speaker = $query->query($this->getCollection(new HydratorSingleObject()));

        if ($speaker->count() === 0) {
            return null;
        }

        return $speaker->first();
    }

    /**
     * Retourne `true` si le speaker avec l'email ($email) a soumis au moins 1 CFP pour l'évènement ($event) passé en paramètre.
     *
     * @param string $email
     *
     */
    public function hasCFPSubmitted(Event $event, $email): bool
    {
        $query = $this->getPreparedQuery(
            'SELECT COUNT(afup_conferenciers.conferencier_id) AS cfp
        FROM afup_conferenciers
        JOIN afup_conferenciers_sessions ON (afup_conferenciers_sessions.conferencier_id = afup_conferenciers.conferencier_id)
        JOIN afup_sessions ON (afup_conferenciers_sessions.session_id = afup_sessions.session_id)
        WHERE afup_sessions.id_forum = :eventId AND afup_conferenciers.email = :email'
        )->setParams(['eventId' => $event->getId(), 'email' => $email]);

        return $query->query()->first()[0]->cfp > 0;
    }

    /**
     * @param string|null $filter
     *
     * @return CollectionInterface&iterable<Speaker>
     */
    public function searchSpeakers(Event $event, $sort = 'name', $direction = 'asc', $filter = null)
    {
        $sorts = [
            'name' => 'c.nom',
            'company' => 'c.societe',
        ];
        Assertion::keyExists($sorts, $sort);
        Assertion::inArray($direction, ['asc', 'desc']);
        $params = ['eventId' => $event->getId()];
        $filterCondition = '';
        if ($filter) {
            $filterCondition = 'AND CONCAT(c.nom, c.prenom, c.societe) LIKE :filter';
            $params['filter'] = '%' . $filter . '%';
        }
        $query = $this->getPreparedQuery(<<<SQL
SELECT c.*
FROM afup_conferenciers c
WHERE c.id_forum = :eventId
$filterCondition
GROUP BY c.conferencier_id, c.nom
ORDER BY $sorts[$sort] $direction
SQL
        )->setParams($params);

        return $query->query($this->getCollection(new HydratorSingleObject()));
    }

    public function countByEvent(Event $event): int
    {
        $query = $this->getPreparedQuery('SELECT COUNT(*) AS nb FROM (SELECT nom, prenom FROM afup_conferenciers WHERE id_forum = :eventId GROUP BY nom, prenom) c')
            ->setParams(['eventId' => $event->getId()]);

        return (int) $query->query()->first()[0]->nb;
    }

    /**
     * @inheritDoc
     */
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);

        $metadata->setEntity(Speaker::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_conferenciers');

        $metadata
            ->addField([
                'columnName' => 'conferencier_id',
                'fieldName' => 'id',
                'primary'       => true,
                'autoincrement' => true,
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'id_forum',
                'fieldName' => 'eventId',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'civilite',
                'fieldName' => 'civility',
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
                'columnName' => 'ville',
                'fieldName' => 'locality',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'phone_number',
                'fieldName' => 'phoneNumber',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'biographie',
                'fieldName' => 'biography',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'twitter',
                'fieldName' => 'twitter',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'mastodon',
                'fieldName' => 'mastodon',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'bluesky',
                'fieldName' => 'bluesky',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'user_github',
                'fieldName' => 'user',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'photo',
                'fieldName' => 'photo',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'will_attend_speakers_diner',
                'fieldName' => 'willAttendSpeakersDiner',
                'type' => 'bool',
                'serializer' => Boolean::class,
            ])
            ->addField([
                'columnName' => 'has_special_diet',
                'fieldName' => 'hasSpecialDiet',
                'type' => 'bool',
                'serializer' => Boolean::class,
            ])
            ->addField([
                'columnName' => 'special_diet_description',
                'fieldName' => 'specialDietDescription',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'hotel_nights',
                'fieldName' => 'hotelNights',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'referent_person',
                'fieldName' => 'referentPerson',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'referent_person_email',
                'fieldName' => 'referentPersonEmail',
                'type' => 'string',
            ])
        ;

        return $metadata;
    }
}
