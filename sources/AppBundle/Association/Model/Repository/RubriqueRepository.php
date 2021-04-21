<?php

namespace AppBundle\Association\Model\Repository;
use CCMBenchmark\Ting\Exception;
use AppBundle\Association\Model\Rubriques;
use Afup\Site\Corporate\Rubrique;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class RubriqueRepository extends Repository implements MetadataInitializer
{
    public function loadRubriqueById($id)
    {
        return $this->getOneBy([
            'id' => $id,
        ]);
    }

    /**
     * @param SerializerFactoryInterface $serializerFactory
     *
     * @return Metadata
     * @throws Exception
     */
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);

        $metadata->setEntity(Rubrique::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_site_rubrique');

        $metadata/*
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
            ])*/
            ->addField([
                'columnName' => 'nom',
                'fieldName' => 'nom',
                'type' => 'string',
            ])
           /* ->addField([
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
            ])*/
            
        ;

        return $metadata;
    }
}
