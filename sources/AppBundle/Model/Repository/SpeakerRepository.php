<?php


namespace AppBundle\Model\Repository;


use AppBundle\Model\Speaker;
use AppBundle\Model\Talk;
use CCMBenchmark\Ting\Repository\HydratorSingleObject;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class SpeakerRepository extends Repository implements MetadataInitializer
{
    /**
     * @param Talk $talk
     * @return \CCMBenchmark\Ting\Repository\CollectionInterface
     */
    public function getSpeakersByTalk(Talk $talk)
    {
        $query = $this->getPreparedQuery('SELECT c.conferencier_id, c.id_forum, c.civilite, c.nom, c.prenom, c.email,c.societe,
        c.biographie, c.twitter, c.user_github, c.photo
        FROM afup_conferenciers c
        LEFT JOIN afup_conferenciers_sessions cs ON cs.conferencier_id = c.conferencier_id
        WHERE cs.session_id = :talkId
        ')->setParams(['talkId' => $talk->getId()]);

        return $query->query($this->getCollection(new HydratorSingleObject()));
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
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'id_forum',
                'fieldName' => 'eventId',
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'civilite',
                'fieldName' => 'civility',
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
                'columnName' => 'biographie',
                'fieldName' => 'biography',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'twitter',
                'fieldName' => 'twitter',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'user_github',
                'fieldName' => 'user',
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'photo',
                'fieldName' => 'photo',
                'type' => 'string'
            ])
        ;

        return $metadata;
    }
}
