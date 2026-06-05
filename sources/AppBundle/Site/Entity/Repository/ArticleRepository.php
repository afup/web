<?php

declare(strict_types=1);

namespace AppBundle\Site\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Site\Entity\Article;
use AppBundle\Site\Entity\Rubrique;
use AppBundle\Site\Enum\ArticleEtat;
use Doctrine\ORM\QueryBuilder;
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
     * @return int[]
     */
    public function getAllYears(): array
    {
        return $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('YEAR(FROM_UNIXTIME(a.date)) as annee')
            ->from('afup_site_article', 'a')
            ->where('a.id_site_rubrique = :rubriqueId')
            ->andWhere('a.etat = :etat')
            ->groupBy('YEAR(FROM_UNIXTIME(a.date))')
            ->orderBy('YEAR(FROM_UNIXTIME(a.date))', 'DESC')
            ->setParameter('rubriqueId', Rubrique::ID_RUBRIQUE_ACTUALITES)
            ->setParameter('etat', ArticleEtat::EnLigne->value)
            ->executeQuery()
            ->fetchFirstColumn();
    }

    /**
     * @return array<int, string>
     */
    public function getEventsLabelsById(): array
    {
        $rows = $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('e.id', 'e.titre')
            ->from('afup_site_article', 'a')
            ->innerJoin('a', 'afup_forum', 'e', 'a.id_forum = e.id')
            ->groupBy('e.id')
            ->orderBy('e.date_debut', 'DESC')
            ->executeQuery()
            ->fetchAllAssociative();

        return array_column($rows, 'titre', 'id');
    }

    public function countPublishedArticles(array $filtres): int
    {
        return (int) $this->createQueryBuilderPublishedActualites($filtres)
            ->select('COUNT(a.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return Article[]
     */
    public function findPublishedArticles(int $page, int $articlesParPage, array $filtres): array
    {
        return $this->createQueryBuilderPublishedActualites($filtres)
            ->setFirstResult(($page - 1) * $articlesParPage)
            ->setMaxResults($articlesParPage)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Article[]
     */
    public function findAllPublishedArticles(array $filtres = []): array
    {
        return $this->createQueryBuilderPublishedActualites($filtres)
            ->getQuery()
            ->getResult();
    }

    public function findPrevious(Article $article): ?Article
    {
        if (!($datePublication = $article->datePublication) instanceof \DateTime) {
            return null;
        }

        $filtres = [
            'before_date' => $datePublication->getTimestamp(),
        ];

        return $this->createQueryBuilderPublishedActualites($filtres)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findNext(Article $article): ?Article
    {
        if (!($datePublication = $article->datePublication) instanceof \DateTime) {
            return null;
        }

        $filtres = [
            'after_date' => $datePublication->getTimestamp(),
        ];

        return $this->createQueryBuilderPublishedActualites($filtres, 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findArticleBySlug(string $slug): ?Article
    {
        return $this->createQueryBuilder('a')
            ->where('a.rubrique = :rubriqueId')
            ->andWhere("CONCAT(a.id, '-', a.raccourci) = :slug")
            ->setParameter('rubriqueId', Rubrique::ID_RUBRIQUE_ACTUALITES)
            ->setParameter('slug', $slug)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findBySlug(string $slug): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere("CONCAT(a.id, '-', a.raccourci) = :slug")
            ->setParameter('slug', $slug)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getAllArticlesWithRubriqueAndTheme(string $ordre = 'titre', string $direction = 'desc', string $filtre = ''): array
    {
        if ($direction !== 'desc' && $direction !== 'asc') {
            $direction = 'desc';
        }

        if (!$this->hasColumn($ordre)) {
            $ordre = 'titre';
        }

        return $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('a.*', 'r.nom as nom_rubrique', 'f.titre as nom_forum')
            ->from('afup_site_article', 'a')
            ->innerJoin('a', 'afup_site_rubrique', 'r', 'a.id_site_rubrique = r.id')
            ->leftJoin('a', 'afup_forum', 'f', 'a.id_forum = f.id')
            ->where('a.titre LIKE :filtre OR a.contenu LIKE :filtre')
            ->orderBy($ordre, $direction)
            ->setParameter('filtre', '%' . $filtre . '%')
            ->executeQuery()
            ->fetchAllAssociative();
    }

    /**
     * @return Article[]
     */
    public function findListForHome(): array
    {
        return $this->createQueryBuilder('a')
            ->innerJoin('a.rubrique', 'r')
            ->where('a.etat = :etat')
            ->andWhere('a.datePublication <= UNIX_TIMESTAMP()')
            ->setParameter('etat', ArticleEtat::EnLigne)
            ->andWhere('r.idParent <> ' . Rubrique::ID_RUBRIQUE_FORUM)
            ->andWhere('r.id <> ' . Rubrique::ID_RUBRIQUE_ASSOCIATION)
            ->andWhere('r.id <> ' . Rubrique::ID_RUBRIQUE_ANTENNES)
            ->andWhere('r.id <> ' . Rubrique::ID_RUBRIQUE_NOS_ACTIONS)
            ->orderBy('a.datePublication', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }

    private function createQueryBuilderPublishedActualites(array $filtres, string $ordre = 'DESC'): QueryBuilder
    {
        $qb = $this->createQueryBuilder('a')
            ->where('a.rubrique = :rubriqueId')
            ->andWhere('a.etat = :etat')
            ->andWhere('a.datePublication <= UNIX_TIMESTAMP()')
            ->setParameter('etat', ArticleEtat::EnLigne)
            ->setParameter('rubriqueId', Rubrique::ID_RUBRIQUE_ACTUALITES)
            ->orderBy('a.datePublication', $ordre);

        if (!empty($filtres['year'])) {
            $qb->andWhere($qb->expr()->in('YEAR(FROM_UNIXTIME(a.datePublication))', ':years'))
                ->setParameter('years', array_map(fn($year): int => (int) $year, $filtres['year']));
        }

        if (!empty($filtres['theme'])) {
            $qb->andWhere($qb->expr()->in('a.theme', ':themes'))
               ->setParameter('themes', $filtres['theme']);
        }

        if (!empty($filtres['event'])) {
            $qb->andWhere($qb->expr()->in('a.idEvent', ':event'))
               ->setParameter('event', $filtres['event']);
        }

        if (isset($filtres['after_date'])) {
            $qb->andWhere('a.datePublication > :afterDate')
               ->setParameter('afterDate', $filtres['after_date']);
        }

        if (isset($filtres['before_date'])) {
            $qb->andWhere('a.datePublication < :beforeDate')
               ->setParameter('beforeDate', $filtres['before_date']);
        }

        return $qb;
    }
}
