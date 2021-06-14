<?php

namespace AppBundle\Site\Model\Repository;

use AppBundle\Site\Model\Rubrique;
use CCMBenchmark\Ting\Repository\HydratorArray;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class RubriqueRepository extends Repository implements MetadataInitializer
{
    public function getAllRubriques($ordre = 'nom', $direction='asc', $filtre = '%')
    {
        if ($direction !== 'desc' && $direction !== 'asc') {
            $direction = 'asc';
        }
        $metadata = $this->getMetadata();
        $columnNameFound = false;
        foreach ($metadata->getFields() as $field) {
            if ($field['columnName'] === $ordre) {
                $columnNameFound=true;
                break;
            }
        }
        if ($columnNameFound === false) {
            $ordre = 'nom';
        }
        $requete = 'SELECT * FROM afup_site_rubrique WHERE afup_site_rubrique.nom LIKE :filtre ';
        $requete .= 'ORDER BY ' . $ordre . ' ' . $direction;
        $query = $this->getQuery($requete);
        $query->setParams(['filtre' => '%' . $filtre . '%']);
        return $query->query($this->getCollection(new HydratorArray()));
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
                'primary' => true,
                'autoincrement' => true,
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'id_parent',
                'fieldName' => 'idParent',
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
                'type' => 'datetime',
                'view_timezone' => 'Europe/Paris',
                'serializer_options' => [
                    'unserialize' => [
                        'unSerializeUseFormat' => true,
                        'format' => 'U',
                    ],
                    'serialize' => [
                        'format' => 'U',
                    ],
                ],
            ])
            ->addField([
                'columnName' => 'etat',
                'fieldName' => 'etat',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'id_personne_physique',
                'fieldName' => 'idPersonnePhysique',
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
                'fieldName' => 'feuilleAssociee',
                'type' => 'int',
            ])
        ;
        return $metadata;
    }
}
