<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests\TechLetter;

use Afup\Tests\Support\IntegrationTestCase;
use AppBundle\TechLetter\Model\Article;
use AppBundle\TechLetter\Model\News;
use AppBundle\TechLetter\Model\Project;
use AppBundle\TechLetter\Model\TechLetter;
use AppBundle\TechLetter\PreviouslySharedLinks;
use AppBundle\Veille\Entity\Envoi;
use AppBundle\Veille\Entity\Repository\EnvoiRepository;

final class PreviouslySharedLinksTest extends IntegrationTestCase
{
    public function testFindInOnlyLooksAtTechLettersSentToMailchimp(): void
    {
        $envoiRepository = self::getContainer()->get(EnvoiRepository::class);

        $envoiRepository->save($this->envoi('2026-09-16 09:00:00', true, new TechLetter(
            projects: [new Project('https://github.com/afup/web', 'afup/web', 'Le site')],
        )));
        $envoiRepository->save($this->envoi('2026-10-21 09:00:00', true, new TechLetter(
            firstNews: new News('https://example.com/news', 'News', new \DateTimeImmutable('2026-10-20')),
            articles: [new Article('http://www.example.com/article/', 'Article', 'example.com', '5', 'Excerpt', 'fr')],
        )));
        $envoiRepository->save($this->envoi('2026-10-08 09:00:00', true, new TechLetter(
            articles: [new Article('https://example.com/article', 'Article', 'example.com', '5', 'Excerpt', 'fr')],
        )));
        // Not sent yet: its links are not "already shared"
        $envoiRepository->save($this->envoi('2026-10-30 09:00:00', false, new TechLetter(
            articles: [new Article('https://example.com/draft', 'Draft', 'example.com', '5', 'Excerpt', 'fr')],
        )));

        $sharedLinks = self::getContainer()->get(PreviouslySharedLinks::class)->findIn(new TechLetter(
            firstNews: new News('https://example.com/news', 'News', new \DateTimeImmutable('2026-11-02')),
            articles: [
                new Article('https://example.com/article', 'Article', 'example.com', '5', 'Excerpt', 'fr'),
                new Article('https://example.com/draft', 'Draft', 'example.com', '5', 'Excerpt', 'fr'),
            ],
            projects: [new Project('https://github.com/afup/web/', 'afup/web', 'Le site')],
        ));

        self::assertSame([
            'https://example.com/news' => ['2026-10-21'],
            'https://example.com/article' => ['2026-10-21', '2026-10-08'],
            'https://github.com/afup/web/' => ['2026-09-16'],
        ], array_map(
            static fn(array $dates): array => array_map(static fn(\DateTimeInterface $date): string => $date->format('Y-m-d'), $dates),
            $sharedLinks,
        ));
    }

    private function envoi(string $date, bool $sent, TechLetter $techLetter): Envoi
    {
        $envoi = new Envoi();
        $envoi->dateEnvoi = new \DateTime($date);
        $envoi->envoyeMailchimp = $sent;
        $envoi->contenu = json_encode($techLetter->jsonSerialize(), JSON_THROW_ON_ERROR);

        return $envoi;
    }
}
