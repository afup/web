<?php

namespace AppBundle\Site\Model\Repository;

use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Repository\HydratorArray;
use CCMBenchmark\Ting\Repository\HydratorSingleObject;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use AppBundle\Site\Model\Rubrique;

class RubriqueRepository extends Repository implements MetadataInitializer
{

    public function getAllRubriques()
    {
        $sql = "
        SELECT *
        FROM afup_site_rubrique
        WHERE 1
        ";

        $query = $this->getPreparedQuery($sql);

        $array = [];
        foreach ($query->query($this->getCollection(new HydratorArray()))->getIterator() as $row) {
            $array[] = $row;
        }

        return $array;
    }
    /**
     * @inheritDoc
     */
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);

        $metadata->setEntity(Rubrique::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_site_rubrique');

        $metadata
        ->addField([
            'columnName' => 'id',
            'fieldName' => 'id',
            'primary'       => true,
            'autoincrement' => true,
            'type' => 'int',
        ])
        ->addField([
            'columnName' => 'id_parent',
            'fieldName' => 'id_parent',
            'type' => 'int',
        ]) 
        ->addField([
            'columnName' => 'nom',
            'fieldName' => 'nom',
            'type' => 'string',
        ])
        ->addField([
            'columnName' => 'raccourci',
            'fieldName' => 'raccourci',
            'type' => 'string',
        ])
        ->addField([
            'columnName' => 'contenu',
            'fieldName' => 'contenu',
            'type' => 'string',
        ])
        ->addField([
            'columnName' => 'descriptif',
            'fieldName' => 'descriptif',
            'type' => 'string',
        ])
        ->addField([
            'columnName' => 'position',
            'fieldName' => 'position',
            'type' => 'int',
        ])
        ->addField([
            'columnName' => 'date',
            'fieldName' => 'date',
            'type' => 'int',
        ])
        ->addField([
            'columnName' => 'etat',
            'fieldName' => 'etat',
            'type' => 'int',
        ]) 
        ->addField([
            'columnName' => 'id_personne_physique',
            'fieldName' => 'id_personne_physique',
            'type' => 'int',
        ])
        ->addField([
            'columnName' => 'icone',
            'fieldName' => 'icone',
            'type' => 'string',
        ])
        ->addField([
            'columnName' => 'pagination',
            'fieldName' => 'pagination',
            'type' => 'int',
        ])
        ->addField([
            'columnName' => 'feuille_associee',
            'fieldName' => 'feuille_associee',
            'type' => 'int',
        ])
        
    ;

    return $metadata;
    }
}