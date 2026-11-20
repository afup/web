<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests\Subscriber;

use Afup\Tests\Support\IntegrationTestCase;
use AppBundle\Event\Model\Talk;
use AppBundle\Subscriber\SitemapXmlSubscriber;
use Doctrine\DBAL\Connection;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Service\UrlContainerInterface;
use Presta\SitemapBundle\Sitemap\Url\Url;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class SitemapXmlSubscriberTest extends IntegrationTestCase
{
    private const int PAST_START = 1451638800; // 2016-01-01 10:00 Europe/Paris
    private const int PAST_END = 1451641200; // 2016-01-01 10:40 Europe/Paris

    public function testTalksSectionContainsOnlyPublishedFacetsWithoutDuplicates(): void
    {
        $connection = self::getContainer()->get(Connection::class);

        $this->insertEvent($connection, 10, 'PHP Tour 2015');
        $this->insertEvent($connection, 20, 'Forum PHP 2016');
        $this->insertEvent($connection, 30, 'Forum PHP 2030');

        // Talk publié, avec un speaker au nom vide (entrée de test) à ignorer
        $this->insertTalk($connection, 1, 10, 'Premier talk', true);
        $this->insertPlanning($connection, 1, 10, self::PAST_START, self::PAST_END);
        $this->insertSpeaker($connection, 1, 10, 'Jane', 'Doe');
        $this->insertSpeaker($connection, 2, 10, '', '');
        $this->linkSpeaker($connection, 1, 1);
        $this->linkSpeaker($connection, 1, 2);

        // Talk publié sur deux créneaux, par la même personne inscrite à un autre CFP
        $this->insertTalk($connection, 2, 20, 'Second talk', true);
        $this->insertPlanning($connection, 2, 20, self::PAST_START, self::PAST_END);
        $this->insertPlanning($connection, 2, 20, self::PAST_START + 3600, self::PAST_END + 3600);
        $this->insertSpeaker($connection, 3, 20, 'Jane', 'DOE');
        $this->linkSpeaker($connection, 2, 3);

        // Talk refusé : ni le speaker ni l'événement ne doivent apparaître
        $this->insertTalk($connection, 3, 30, 'Talk refusé', false);
        $this->insertSpeaker($connection, 4, 30, 'Refused', 'Speaker');
        $this->linkSpeaker($connection, 3, 4);

        // Clinique passée : non visible dans l'historique
        $this->insertTalk($connection, 4, 30, 'Clinique', true, Talk::TYPE_CLINIC);
        $this->insertPlanning($connection, 4, 30, self::PAST_START, self::PAST_END);
        $this->insertSpeaker($connection, 5, 30, 'Clinic', 'Speaker');
        $this->linkSpeaker($connection, 4, 5);

        $talksUrls = $this->populate()['talks'];
        $talksLocs = array_map(static fn(UrlConcrete $url): string => $url->getLoc(), $talksUrls);
        $talksUrlsByLoc = array_combine($talksLocs, $talksUrls);

        self::assertSame(array_values(array_unique($talksLocs)), $talksLocs, 'Aucune <loc> en double dans sitemap.talks.xml');

        self::assertSame(
            [
                'http://localhost/talks/1-premier-talk',
                'http://localhost/talks/2-second-talk',
                'http://localhost/talks/?fR%5Bspeakers.label%5D%5B0%5D=Jane%20DOE',
                'http://localhost/talks/?fR%5Bevent.title%5D%5B0%5D=PHP%20Tour%202015',
                'http://localhost/talks/?fR%5Bevent.title%5D%5B0%5D=Forum%20PHP%202016',
            ],
            $talksLocs,
        );

        $talkUrl = $talksUrlsByLoc['http://localhost/talks/1-premier-talk'];
        self::assertSame(self::PAST_END, $talkUrl->getLastmod()?->getTimestamp(), 'lastmod = fin du créneau de planning');

        $eventUrl = $talksUrlsByLoc['http://localhost/talks/?fR%5Bevent.title%5D%5B0%5D=PHP%20Tour%202015'];
        self::assertNull($eventUrl->getChangefreq());
        self::assertNull($eventUrl->getPriority());
    }

    /**
     * @return array<string, list<UrlConcrete>> URLs par section, dans l'ordre d'ajout (doublons compris)
     */
    private function populate(): array
    {
        $container = new class implements UrlContainerInterface {
            /** @var array<string, list<UrlConcrete>> */
            public array $urls = [];

            public function addUrl(Url $url, string $section): void
            {
                if ($url instanceof UrlConcrete) {
                    $this->urls[$section][] = $url;
                }
            }
        };

        self::getContainer()->get(SitemapXmlSubscriber::class)->populate(
            new SitemapPopulateEvent($container, self::getContainer()->get(UrlGeneratorInterface::class)),
        );

        return $container->urls;
    }

    private function insertEvent(Connection $connection, int $id, string $title): void
    {
        $connection->insert('afup_forum', [
            'id' => $id,
            'titre' => $title,
            'path' => 'event-' . $id,
            'date_debut' => '2016-01-01',
            'date_fin' => '2016-01-02',
        ]);
    }

    private function insertTalk(Connection $connection, int $id, int $eventId, string $title, bool $scheduled, int $type = Talk::TYPE_FULL_LONG): void
    {
        $connection->insert('afup_sessions', [
            'session_id' => $id,
            'id_forum' => $eventId,
            'date_soumission' => '2015-06-01',
            'titre' => $title,
            'abstract' => 'Abstract de test',
            'genre' => $type,
            'plannifie' => $scheduled ? 1 : 0,
        ]);
    }

    private function insertPlanning(Connection $connection, int $talkId, int $eventId, int $start, int $end): void
    {
        $connection->insert('afup_forum_planning', [
            'id_session' => $talkId,
            'id_forum' => $eventId,
            'debut' => $start,
            'fin' => $end,
            'id_salle' => 1,
            'keynote' => 0,
        ]);
    }

    private function insertSpeaker(Connection $connection, int $id, int $eventId, string $firstname, string $lastname): void
    {
        $connection->insert('afup_conferenciers', [
            'conferencier_id' => $id,
            'id_forum' => $eventId,
            'prenom' => $firstname,
            'nom' => $lastname,
            'email' => sprintf('speaker%d@example.com', $id),
        ]);
    }

    private function linkSpeaker(Connection $connection, int $talkId, int $speakerId): void
    {
        $connection->insert('afup_conferenciers_sessions', [
            'session_id' => $talkId,
            'conferencier_id' => $speakerId,
        ]);
    }
}
