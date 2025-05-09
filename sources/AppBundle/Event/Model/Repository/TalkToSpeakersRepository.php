<?php

declare(strict_types=1);

namespace AppBundle\Event\Model\Repository;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Speaker;
use AppBundle\Event\Model\Talk;
use AppBundle\Event\Model\TalkToSpeaker;
use CCMBenchmark\Ting\Query\QueryException;
use CCMBenchmark\Ting\Repository\HydratorArray;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class TalkToSpeakersRepository extends Repository implements MetadataInitializer
{
    public function getNumberOfSpeakers(Event $event, \DateTime $since = null)
    {
        $sql = 'SELECT COUNT(distinct conferencier_id) AS count 
                FROM afup_conferenciers_sessions
                JOIN afup_sessions ON (afup_conferenciers_sessions.session_id = afup_sessions.session_id)
                WHERE id_forum = :event
        ';
        $params = ['event' => $event->getId()];
        if ($since instanceof \DateTime) {
            $sql .= ' AND afup_sessions.date_soumission >= :since ';
            $params['since'] = $since->format('Y-m-d');
        }
        $query = $this->getQuery($sql);
        $query->setParams($params);

        return $query->query($this->getCollection(new HydratorArray()))->first()['count'];
    }

    /**
     * @param Speaker[] $speakers
     * @throws QueryException
     */
    public function replaceSpeakers(Talk $talk, array $speakers): void
    {
        $this->startTransaction();
        try {
            $delete = $this->getPreparedQuery('DELETE FROM afup_conferenciers_sessions WHERE talk_id = :talk');
            $delete->setParams(['talk' => $talk->getId()])->execute();

            $insert = $this->getPreparedQuery('INSERT INTO afup_conferenciers_sessions (conferencier_id, session_id) VALUES (:speaker, :talk)');
            foreach ($speakers as $speaker) {
                $insert->setParams(['speaker' => $speaker->getId(), 'talk' => $talk->getId()])->execute();
            }
            $this->commit();
        } catch (QueryException $exception) {
            $this->rollback();
            throw $exception;
        }
    }

    public function addSpeakerToTalk(Talk $talk, Speaker $speaker): void
    {
        $insert = $this->getPreparedQuery('REPLACE INTO afup_conferenciers_sessions (conferencier_id, session_id) VALUES (:speaker, :talk)');
        $insert->setParams(['speaker' => $speaker->getId(), 'talk' => $talk->getId()])->execute();
    }

    /**
     * @inheritDoc
     */
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);

        $metadata->setEntity(TalkToSpeaker::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_conferenciers_sessions');

        $metadata
            ->addField([
                'columnName' => 'conferencier_id',
                'fieldName' => 'speakerId',
                'primary'       => true,
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'session_id',
                'fieldName' => 'talkId',
                'primary'       => true,
                'type' => 'int',
            ])
        ;

        return $metadata;
    }
}
