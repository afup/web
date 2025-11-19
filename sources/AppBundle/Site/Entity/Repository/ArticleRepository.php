<?php

declare(strict_types=1);

namespace AppBundle\Site\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Doctrine\Direction;
use AppBundle\Site\Entity\Article;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<Article>
 */
final class ArticleRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * @return array<Article>
     */
    public function getAllArticlesWithCategoryAndTheme(string $sort, Direction $direction, string $filtre): array
    {
        $sortableFields = ['article.publishedAt', 'article.title', 'article.state', 'rubric.name'];
        if (!in_array($sort, $sortableFields, true)) {
            $sort = 'article.publishedAt';
        }

        $query = ($qb = $this->createQueryBuilder('article'))
            ->select('partial article.{id, publishedAt, title, theme, state}', 'partial rubric.{id, name}', 'partial event.{id, title}')
            ->innerJoin('article.rubric', 'rubric')
            ->leftJoin('article.event', 'event')
            ->orderBy($sort, $direction->value);

        if ($filtre !== '') {
            $query = $query->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('article.title', ':filtre'),
                    $qb->expr()->like('article.content', ':filtre'),
                ),
            )
            ->setParameter('filtre', '%' . $filtre . '%');
        }

        return $query->getQuery()->execute();
    }
}
