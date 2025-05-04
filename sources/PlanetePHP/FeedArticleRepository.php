<?php

declare(strict_types=1);

namespace PlanetePHP;

use Assert\Assertion;
use Doctrine\DBAL\Connection;
use PDO;

class FeedArticleRepository
{
    const RELEVANT = 1;
    const IRRELEVANT = 0;
    const PERTINENCE_LIST = 'php|afup|pear|pecl|symfony|copix|jelix|wampserver|simpletest|simplexml|zend|pmo|drupal|ovidentia|mvc|magento|chrome|spip|PDO|mock|cake|hiphop|CMS|Framework|typo3|photon|pattern';
    private readonly string $pertinenceRegex;

    public function __construct(private readonly Connection $connection)
    {
        $this->pertinenceRegex = '/' . self::PERTINENCE_LIST . '/i';
    }

    public function count(): int
    {
        $query = $this->connection->prepare('SELECT COUNT(b.id) FROM afup_planete_billet b');
        $query->execute();

        return intval($query->fetchColumn());
    }

    public function countRelevant(): int
    {
        $query = $this->connection->prepare('SELECT COUNT(b.id) FROM afup_planete_billet b WHERE b.etat = :status');
        $query->bindValue('status', self::RELEVANT);
        $query->execute();

        return intval($query->fetchColumn());
    }

    /**
     * @param string $sort
     * @param string $direction
     * @param int    $limit
     *
     * @return FeedArticle[]
     */
    public function search($sort, $direction, $limit = 20): array
    {
        $sorts = [
            'title' => 'b.titre',
            'content' => 'b.contenu',
            'status' => 'b.etat',
        ];
        Assertion::integer($limit);
        Assertion::keyExists($sorts, $sort);
        Assertion::inArray($direction, ['asc', 'desc']);
        $qb = $this->connection->createQueryBuilder();
        $qb->from('afup_planete_billet', 'b')
            ->select('b.*')
            ->orderBy($sorts[$sort], $direction)
            ->setMaxResults($limit);

        return $this->hydrate($qb->execute()->fetchAll());
    }

    public function save(FeedArticle $billet)
    {
        $id = $this->findIdByKey($billet->getKey());
        if (null !== $id) {
            return $this->update($billet, $id);
        }

        return $this->insert($billet);
    }

    /**
     * @return array<DisplayableFeedArticle>
     */
    public function findLatest($page = 0, $format = DATE_ATOM, $nombre = 10): array
    {
        $query = $this->connection->prepare('SELECT b.titre, b.url, b.maj, b.auteur, b.contenu, f.nom feed_name, f.url feed_url
            FROM afup_planete_billet b
            INNER JOIN afup_planete_flux f on b.afup_planete_flux_id = f.id
            WHERE b.etat = :status
            ORDER BY b.maj DESC
            LIMIT :start, :length
        ');
        $query->bindValue('status', self::RELEVANT);
        $query->bindValue('start', $page * $nombre, PDO::PARAM_INT);
        $query->bindValue('length', $nombre, PDO::PARAM_INT);
        $query->execute();

        return $this->hydrateDisplayable($query->fetchAll(), $format);
    }

    public function isRelevant($content): int
    {
        $content = strip_tags((string) $content);
        $relevant = self::IRRELEVANT;
        if (preg_match($this->pertinenceRegex, $content)) {
            $relevant = self::RELEVANT;
        }

        return $relevant;
    }

    private function findIdByKey($key)
    {
        $query = $this->connection->prepare('SELECT id FROM afup_planete_billet WHERE clef = :key');
        $query->bindValue('key', $key);
        $query->execute();
        $row = $query->fetch();

        return is_array($row) ? $row['id'] : null;
    }

    private function update(FeedArticle $billet, $id = null)
    {
        return $this->connection->executeUpdate('UPDATE afup_planete_billet
            SET afup_planete_flux_id = :feedId,
                clef= :key,
                titre = :title,
                url = :url,
                maj = :update,
                auteur = :author,
                resume = :summary,
                contenu = :content,
                etat = :status
            WHERE id = :id', [
            'id' => $id ?: $billet->getId(),
            'feedId' => $billet->getFeedId(),
            'key' => $billet->getKey(),
            'title' => $billet->getTitle(),
            'url' => $billet->getUrl(),
            'update' => $billet->getUpdate(),
            'author' => $billet->getAuthor(),
            'summary' => $billet->getSummary(),
            'content' => $billet->getContent(),
            'status' => $billet->getStatus(),
        ]);
    }

    private function insert(FeedArticle $billet)
    {
        return $this->connection->executeUpdate('INSERT INTO afup_planete_billet 
            (afup_planete_flux_id, clef, titre, url, maj, auteur, resume, contenu, etat) 
            VALUES (:feedId, :key, :title, :url, :update, :author, :summary, :content, :status)', [
            'feedId' => $billet->getFeedId(),
            'key' => $billet->getKey(),
            'title' => $billet->getTitle(),
            'url' => $billet->getUrl(),
            'update' => $billet->getUpdate(),
            'author' => $billet->getAuthor(),
            'summary' => $billet->getSummary(),
            'content' => $billet->getContent(),
            'status' => $billet->getStatus(),
        ]);
    }

    private function hydrate(array $rows): array
    {
        return array_map(static fn (array $row): FeedArticle => new FeedArticle(
            $row['id'],
            $row['afup_planete_flux_id'],
            $row['clef'],
            $row['titre'],
            $row['url'],
            $row['maj'],
            $row['auteur'],
            $row['resume'],
            $row['contenu'],
            $row['etat']
        ), $rows);
    }

    /**
     * @return array<DisplayableFeedArticle>
     */
    private function hydrateDisplayable(array $rows, $format = DATE_ATOM): array
    {
        return array_map(static fn (array $row): DisplayableFeedArticle => new DisplayableFeedArticle(
            $row['titre'],
            $row['url'],
            date($format, (int) $row['maj']),
            $row['auteur'],
            $row['contenu'],
            $row['feed_name'],
            $row['feed_url']
        ), $rows);
    }
}
