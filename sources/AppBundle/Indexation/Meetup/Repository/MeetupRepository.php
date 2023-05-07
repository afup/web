<?php

namespace AppBundle\Indexation\Meetup\Repository;

use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\Repository;
use AppBundle\Indexation\Meetup\Entity\Meetup;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class MeetupRepository extends Repository implements MetadataInitializer
{
    /**
     * @param Meetup $meetup
     * @param string $antenne
     * @return void
     */
    public function saveMeetup(Meetup $meetup, $antenne)
    {
        $antenneId = $this->getAntenneIdByVille($antenne);

        $sql = "INSERT INTO afup_meetup (date, title, description, location, antenne_id) VALUES (?, ?, ?, ?)";
        $this->get('ting')->executeQuery($sql, [
            $meetup->getDate(), 
            $meetup->getTitle(),  
            $meetup->getDescription(),
            $meetup->getLocation(),
            $antenneId
        ]);
    }

    /**
     * @param string $ville
     * @return int
     */
    private function getAntenneIdByVille($ville)
    {
        $sql = 'SELECT id FROM afup_antenne WHERE ville = :ville';
        $result =  $this->get('ting')->executeQuery($sql, [
            'ville' => $ville
        ]);
        dd($result);
    }

    /**
     * @inheritDoc
     */
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);

        $metadata->setEntity(Meetup::class);
        $metadata->setConnectionName('default');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_meetup');

        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary' => true,
                'autoincrement' => true,
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'date',
                'fieldName' => 'date',
                'type' => 'datetime'
            ])
            ->addField([
                'columnName' => 'title',
                'fieldName' => 'title',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'description',
                'fieldName' => 'description',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'location',
                'fieldName' => 'location',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'antenne_id',
                'fieldName' => 'antenne_id',
                'type' => 'int'
            ]);

        return $metadata;
    }
}
