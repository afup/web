<?php

namespace AppBundle\Site\Model\Repository;

use Afup\Site\Corporate\Rubrique;
use AppBundle\Site\Model\Article;
use CCMBenchmark\Ting\Repository\HydratorArray;
use CCMBenchmark\Ting\Repository\HydratorSingleObject;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class ArticleRepository extends Repository implements MetadataInitializer
{
    /**
     * @inheritDoc
     */
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);

        $metadata->setEntity(Article::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_site_article');

        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary'       => true,
                'autoincrement' => true,
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'id_site_rubrique',
                'fieldName' => 'rubricId',
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'raccourci',
                'fieldName' => 'path',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'titre',
                'fieldName' => 'title',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'chapeau',
                'fieldName' => 'leadParagraph',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'descriptif',
                'fieldName' => 'description',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'contenu',
                'fieldName' => 'content',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'date',
                'fieldName' => 'publishedAt',
                'type' => 'datetime',
                'serializer_options' => [
                    'unserialize' => ['unSerializeUseFormat' => true, 'format' => 'U']
                ]
            ])
        ;

        return $metadata;
    }
}
