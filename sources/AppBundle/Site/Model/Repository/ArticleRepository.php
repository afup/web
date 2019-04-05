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

    public function getEventsLabelsById()
    {
        $sql = "SELECT afup_forum.id, afup_forum.titre
        FROM afup_site_article
        JOIN afup_forum ON (afup_site_article.id_forum = afup_forum.id)
        GROUP BY afup_forum.id
        ORDER BY afup_forum.date_debut DESC
        ";

        $query = $this->getQuery($sql);

        $eventsLabelsById = [];
        foreach ($query->query($this->getCollection(new HydratorArray()))->getIterator() as $row) {
            $eventsLabelsById[$row['id']] = $row['titre'];
        }

        return $eventsLabelsById;
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
        $eventParams = [];
        $yearSqlFilter = '';
        $themeSqlFilter = '';
        $eventSqlFilter = '';
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

        if (isset($filters['event']) && count($filters['event'])) {
            $cpt = 1;
            $eventPreparedParams = [];
            foreach ($filters['event'] as $event) {
                $paramName = 'event_' . $cpt++;
                $eventParams[$paramName] = $event;
                $eventPreparedParams[] = ':' . $paramName;
            }
            $eventSqlFilter = sprintf('AND id_forum IN (%s)', implode(',', $eventPreparedParams));
        }

        $sql  = sprintf('SELECT afup_site_article.*
        FROM afup_site_article
        WHERE afup_site_article.id_site_rubrique = :rubricId
        AND etat = 1
        %s %s %s
        ORDER BY date DESC
        ', $yearSqlFilter, $themeSqlFilter, $eventSqlFilter);

        $params = [
            'rubricId' => Rubrique::ID_RUBRIQUE_ACTUALITES,
        ];

        $params = array_merge($params, $yearParams, $themeParams, $eventParams);

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
                'columnName' => 'type_contenu',
                'fieldName' => 'contentType',
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
