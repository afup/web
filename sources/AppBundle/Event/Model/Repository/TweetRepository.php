<?php

declare(strict_types=1);

namespace AppBundle\Event\Model\Repository;

use AppBundle\Event\Model\Talk;
use AppBundle\Event\Model\Tweet;
use CCMBenchmark\Ting\Repository\HydratorArray;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class TweetRepository extends Repository implements MetadataInitializer
{
    public function getNumberOfTweetsByTalk(Talk $talk): int
    {
        $query = $this->getQuery('SELECT COUNT(id) AS tweets FROM tweet WHERE id_session = :talk_id');
        $query->setParams(['talk_id' => $talk->getId()]);
        return (int) $query->query($this->getCollection(new HydratorArray()))->first()['tweets'];
    }

    /**
     * @param array<Talk> $talks
     * @return array<int, int>
     */
    public function getNumberOfTweetsPerTalk(array $talks): array
    {
        $query = $this->getPreparedQuery('SELECT id_session, COUNT(id_session) AS quantity FROM tweet WHERE id_session IN(:ids) GROUP BY id_session');

        $query->setParams([
            'ids' => array_values(array_map(fn (Talk $talk) => $talk->getId(), $talks)),
        ]);

        $res = $query->query($this->getCollection(new HydratorArray()));

        var_dump($res);

        return [];
    }

    /**
     *
     * @return Metadata
     */
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);
        $metadata->setEntity(Tweet::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('tweet');

        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary' => true,
                'autoincrement' => false,
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'id_session',
                'fieldName' => 'talkId',
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'created_at',
                'fieldName' => 'createdAt',
                'type' => 'datetime',
                'serializer_options' => [
                    'unserialize' => ['unSerializeUseFormat' => false]
                ]

            ])
            ->addField([
                'columnName' => 'social_network',
                'fieldName' => 'socialNetwork',
                'type' => 'string',
            ])
        ;

        return $metadata;
    }
}
