<?php

namespace PlanetePHP;

use Assert\Assertion;
use Doctrine\DBAL\Connection;
use HTMLPurifier;
use HTMLPurifier_Config;
use PDO;

class FeedArticleRepository
{
    const RELEVANT = 1;
    const IRRELEVANT = 0;
    const PERTINENCE_LIST = 'php|afup|pear|pecl|symfony|copix|jelix|wampserver|simpletest|simplexml|zend|pmo|drupal|ovidentia|mvc|magento|chrome|spip|PDO|mock|cake|hiphop|CMS|Framework|typo3|photon|pattern';
    /** @var Connection */
    private $connection;
    /** @var string */
    private $pertinenceRegex;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
        $this->pertinenceRegex = '/'.self::PERTINENCE_LIST.'/i';
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
    public function search($sort, $direction, $limit = 20)
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
     * @param string $content
     * @param string $url
     * @param int    $characters
     *
     * @return string
     */
    public function truncateContent($content, $url, $characters = 3000)
    {
        $truncatedContent = $content;
        $isTruncated = false;
        if (mb_strlen($content) > $characters) {
            $lastPeriod = mb_strpos($content, '.', $characters);
            if ($lastPeriod) {
                $isTruncated = true;
                $truncatedContent = mb_substr($content, 0, $lastPeriod + 1);
            }
        }

        $config = HTMLPurifier_Config::createDefault();
        $config->set('Core.Encoding', 'UTF-8');
        $config->set('HTML.Doctype', 'HTML 4.01 Transitional');
        $purifier = new HTMLPurifier($config);
        $truncatedContent = $purifier->purify($truncatedContent);

        if ($isTruncated) {
            $truncatedContent .= sprintf('<p><a class="btn" href="%s">Voir la suite</a></p>', $url);
        }

        return $truncatedContent;
    }

    /**
     * @param int    $page
     * @param string $format
     *
     * @return DisplayableFeedArticle[]
     */
    public function findLatestTruncated($page = 0, $format = DATE_ATOM)
    {
        return array_map(function (DisplayableFeedArticle $article) {
            $article->setContent($this->truncateContent($article->getContent(), $article->getUrl()));

            return $article;
        }, $this->findLatest($page, $format));
    }

    public function findLatest($page = 0, $format = DATE_ATOM, $nombre = 10)
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

    public function isRelevant($content)
    {
        $content = strip_tags($content);
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

    private function hydrate(array $rows)
    {
        return array_map(static function (array $row) {
            return new FeedArticle(
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
            );
        }, $rows);
    }

    private function hydrateDisplayable(array $rows, $format = DATE_ATOM)
    {
        return array_map(static function (array $row) use ($format) {
            return new DisplayableFeedArticle(
                $row['titre'],
                $row['url'],
                date($format, $row['maj']),
                $row['auteur'],
                $row['contenu'],
                $row['feed_name'],
                $row['feed_url']
            );
        }, $rows);
    }
}
