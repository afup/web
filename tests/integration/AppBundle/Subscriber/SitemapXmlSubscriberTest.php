<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests\Subscriber;

use Afup\Tests\Support\IntegrationTestCase;
use AppBundle\Event\Model\Talk;
use AppBundle\Subscriber\SitemapXmlSubscriber;
use Doctrine\DBAL\Connection;
use Presta\SitemapBundle\Service\UrlContainerInterface;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Presta\SitemapBundle\Sitemap\Url\UrlDecorator;
use Presta\SitemapBundle\Sitemap\Url\Url;

final class SitemapXmlSubscriberTest extends IntegrationTestCase
{
    private const int PAST_START = 1451638800; // 2016-01-01 10:00 Europe/Paris
    private const int PAST_END = 1451641200; // 2016-01-01 10:40 Europe/Paris

    public function testTalksAreListedOncePerTalkAndOnlyWhenPublished(): void
    {
        $connection = self::getContainer()->get(Connection::class);
        $connection->insert('afup_forum', ['id' => 10, 'titre' => 'Forum PHP 2016', 'path' => 'forumphp2016', 'date_debut' => '2016-01-01', 'date_fin' => '2016-01-02']);

        $this->insertTalk($connection, 1, 'Talk publié');
        $this->insertPlanning($connection, 1, self::PAST_START, self::PAST_END);
        // Talk publié sur deux créneaux, avec une vidéo : une seule entrée par section
        $this->insertTalk($connection, 2, 'Talk sur deux créneaux', youtubeId: 'abc123');
        $this->insertPlanning($connection, 2, self::PAST_START, self::PAST_END);
        $this->insertPlanning($connection, 2, self::PAST_START + 3600, self::PAST_END + 3600);
        // Non publiés : clinique, date de publication à venir
        $this->insertTalk($connection, 3, 'Clinique', type: Talk::TYPE_CLINIC);
        $this->insertPlanning($connection, 3, self::PAST_START, self::PAST_END);
        $this->insertTalk($connection, 4, 'Publication à venir', publishedOn: '+1 day');
        $this->insertPlanning($connection, 4, self::PAST_START, self::PAST_END);

        $urls = $this->registerTalksUrls();

        self::assertSame(['http://localhost/talks/1-talk-publie', 'http://localhost/talks/2-talk-sur-deux-creneaux'], $urls['talks']);
        self::assertSame(['http://localhost/talks/2-talk-sur-deux-creneaux'], $urls['video']);
    }

    /**
     * @return array<string, list<string>>
     */
    private function registerTalksUrls(): array
    {
        $container = new class implements UrlContainerInterface {
            /** @var array<string, list<string>> */
            public array $locs = [];

            public function addUrl(Url $url, string $section): void
            {
                $concrete = $url instanceof UrlDecorator ? $url->getUrlDecorated() : $url;
                if ($concrete instanceof UrlConcrete) {
                    $this->locs[$section][] = $concrete->getLoc();
                }
            }
        };

        self::getContainer()->get(SitemapXmlSubscriber::class)->registerTalksUrls($container);

        return $container->locs;
    }

    private function insertTalk(Connection $connection, int $id, string $title, int $type = Talk::TYPE_FULL_LONG, ?string $youtubeId = null, ?string $publishedOn = null): void
    {
        $connection->insert('afup_sessions', [
            'session_id' => $id,
            'id_forum' => 10,
            'date_soumission' => '2015-06-01',
            'titre' => $title,
            'abstract' => 'Abstract de test',
            'genre' => $type,
            'plannifie' => 1,
            'youtube_id' => $youtubeId,
            'date_publication' => null !== $publishedOn ? date('Y-m-d H:i:s', (int) strtotime($publishedOn)) : null,
        ]);
    }

    private function insertPlanning(Connection $connection, int $talkId, int $start, int $end): void
    {
        $connection->insert('afup_forum_planning', ['id_session' => $talkId, 'id_forum' => 10, 'debut' => $start, 'fin' => $end, 'id_salle' => 1, 'keynote' => 0]);
    }
}
