<?php

declare(strict_types=1);

namespace PlanetePHP;

use AppBundle\Doctrine\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Webmozart\Assert\Assert;

/**
 * @extends EntityRepository<Article>
 */
final class ArticleRepository extends EntityRepository
{
    private const string PERTINENCE_REGEX = '/php|afup|pear|pecl|symfony|copix|jelix|wampserver|simpletest|simplexml|zend|pmo|drupal|ovidentia|mvc|magento|chrome|spip|PDO|mock|cake|hiphop|CMS|Framework|typo3|photon|pattern/i';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function countRelevant(): int
    {
        return $this->count([
            'isRelevant' => true,
        ]);
    }

    /**
     * @return Article[]
     */
    public function search(string $sort, string $direction, int $limit = 20): array
    {
        $sorts = [
            'title' => 'a.title',
            'content' => 'a.content',
            'status' => 'a.isRelevant',
        ];
        Assert::keyExists($sorts, $sort);
        Assert::inArray($direction, ['asc', 'desc']);

        return $this->createQueryBuilder('a')
            ->orderBy($sorts[$sort], $direction)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Article[]
     */
    public function findLatest(int $page = 0, int $nombre = 10): array
    {
        return $this->createQueryBuilder('a')
            ->innerJoin('a.feed', 'f')
            ->addSelect('f')
            ->where('a.isRelevant = :isRelevant')
            ->setParameter('isRelevant', true)
            ->orderBy('a.updatedAt', 'DESC')
            ->setFirstResult($page * $nombre)
            ->setMaxResults($nombre)
            ->getQuery()
            ->getResult();
    }

    public function saveByKey(Article $article): void
    {
        $existing = $this->findOneBy(['key' => $article->key]);

        if ($existing === null) {
            $this->save($article);

            return;
        }

        $existing->feed = $article->feed;
        $existing->title = $article->title;
        $existing->url = $article->url;
        $existing->updatedAt = $article->updatedAt;
        $existing->author = $article->author;
        $existing->summary = $article->summary;
        $existing->content = $article->content;
        $existing->isRelevant = $article->isRelevant;

        $this->save($existing);
    }

    public function isRelevant(?string $content): bool
    {
        $content = strip_tags((string) $content);

        return preg_match(self::PERTINENCE_REGEX, $content) === 1;
    }
}
