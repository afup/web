<?php

namespace AppBundle\Event\Model\Repository;

use AppBundle\Event\Model\Speaker;
use AppBundle\Event\Model\Talk;
use AppBundle\Event\Model\TalkToSpeaker;
use CCMBenchmark\Ting\Query\QueryException;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class TalkToSpeakersRepository extends Repository implements MetadataInitializer
{
    /**
     * @param Talk $talk
     * @param Speaker[] $speakers
     * @throws QueryException
     */
    public function replaceSpeakers(Talk $talk, array $speakers)
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

    public function addSpeakerToTalk(Talk $talk, Speaker $speaker)
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
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'session_id',
                'fieldName' => 'talkId',
                'primary'       => true,
                'type' => 'int'
            ])
        ;

        return $metadata;
    }
}
