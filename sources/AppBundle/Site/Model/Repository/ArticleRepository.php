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
    public function getAllYears()
    {
        $sql = "
        SELECT YEAR(FROM_UNIXTIME(afup_site_article.date)) as year
        FROM afup_site_article
        WHERE afup_site_article.id_site_rubrique = :rubricId
          AND etat = 1
        GROUP BY YEAR(FROM_UNIXTIME(afup_site_article.date))
        ORDER BY YEAR(FROM_UNIXTIME(afup_site_article.date)) DESC
        ";

        $query = $this->getPreparedQuery($sql)->setParams(['rubricId' => Rubrique::ID_RUBRIQUE_ACTUALITES]);

        $years = [];
        foreach ($query->query($this->getCollection(new HydratorArray()))->getIterator() as $row) {
            $years[] = $row['year'];
        }

        return $years;
    }

    public function countPublishedNews(array $filters)
    {
        list($sql, $params) = $this->getSqlPublishedNews($filters);

        $sql = sprintf("SELECT COUNT(*) as cnt FROM (%s) as req", $sql);

        $query = $this->getPreparedQuery($sql)->setParams($params);

        $row = $query->query($this->getCollection(new HydratorArray()))->first();

        return $row['cnt'];
    }

    public function findPublishedNews($page, $itemsPerPage, array $filters)
    {
        list($sql, $params) = $this->getSqlPublishedNews($filters);

        $sql .= ' LIMIT :offset, :limit ';

        $params = array_merge(
            $params,
            [
                'offset' => ($page - 1) * $itemsPerPage,
                'limit' => $itemsPerPage
            ]
        );

        $query = $this->getPreparedQuery($sql)->setParams($params);

        return $query->query($this->getCollection(new HydratorSingleObject()));
    }

    private function getSqlPublishedNews(array $filters)
    {
        $yearParams = [];
        $themeParams = [];
        $yearSqlFilter = '';
        $themeSqlFilter = '';
        if (isset($filters['year']) && count($filters['year'])) {
            $cpt = 1;
            $yearPreaparedParams = [];
            foreach ($filters['year'] as $year) {
                $paramName = 'year_' . $cpt++;
                $yearParams[$paramName] = $year;
                $yearPreaparedParams[] = ':' . $paramName;
            }
            $yearSqlFilter = sprintf('AND YEAR(FROM_UNIXTIME(date)) IN (%s)', implode(',', $yearPreaparedParams));
        }

        if (isset($filters['theme']) && count($filters['theme'])) {
            $cpt = 1;
            $themesPreparedParams = [];
            foreach ($filters['theme'] as $theme) {
                $paramName = 'theme_' . $cpt++;
                $themeParams[$paramName] = $theme;
                $themesPreparedParams[] = ':' . $paramName;
            }
            $themeSqlFilter = sprintf('AND theme IN (%s)', implode(',', $themesPreparedParams));
        }

        $sql  = sprintf('SELECT afup_site_article.*
        FROM afup_site_article
        WHERE afup_site_article.id_site_rubrique = :rubricId
        AND etat = 1
        %s %s
        ORDER BY date DESC
        ', $yearSqlFilter, $themeSqlFilter);

        $params = [
            'rubricId' => Rubrique::ID_RUBRIQUE_ACTUALITES,
        ];

        $params = array_merge($params, $yearParams, $themeParams);

        return [$sql, $params];
    }

    /**
     * @param string $slug
     * @return Article|null
     */
    public function findNewsBySlug($slug)
    {
        $query = $this
            ->getPreparedQuery(
                'SELECT * FROM afup_site_article WHERE id_site_rubrique = :rubricId AND etat = 1 AND CONCAT(id, "-", raccourci) = :slug'
            )
            ->setParams(['rubricId' => Rubrique::ID_RUBRIQUE_ACTUALITES, 'slug' => $slug]);
        $events = $query->query($this->getCollection(new HydratorSingleObject()));

        if ($events->count() === 0) {
            return null;
        }

        return $events->first();
    }

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
