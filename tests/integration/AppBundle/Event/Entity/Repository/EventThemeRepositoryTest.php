<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests\Event\Entity\Repository;

use Afup\Tests\Support\IntegrationTestCase;
use AppBundle\Event\Entity\EventTheme;
use AppBundle\Event\Entity\Repository\EventThemeRepository;

final class EventThemeRepositoryTest extends IntegrationTestCase
{
    public function testSaveFindByEventAndDelete(): void
    {
        $eventThemeRepository = self::getContainer()->get(EventThemeRepository::class);

        $themeAtelier = $this->buildEventTheme(42, 'Atelier', 2, 'Description de l\'atelier');
        $themeConference = $this->buildEventTheme(42, 'Conference', 1, null);
        $themeLightning = $this->buildEventTheme(42, 'Lightning talks', 1, null);
        $themeAutreEvenement = $this->buildEventTheme(43, 'Autre evenement', 3, null);

        $eventThemeRepository->save($themeAtelier);
        $eventThemeRepository->save($themeConference);
        $eventThemeRepository->save($themeLightning);
        $eventThemeRepository->save($themeAutreEvenement);

        $loaded = $eventThemeRepository->find($themeAtelier->id);
        self::assertInstanceOf(EventTheme::class, $loaded);
        self::assertSame('Atelier', $loaded->name);
        self::assertSame(42, $loaded->idForum);
        self::assertSame('Description de l\'atelier', $loaded->description);
        self::assertSame(2, $loaded->priority);
        self::assertNull($themeConference->description);

        // Tri attendu : priorité croissante puis nom alphabétique, filtré par évènement
        $themes = $eventThemeRepository->getByThemesOrderedByPriority(42);
        self::assertSame(['Conference', 'Lightning talks', 'Atelier'], array_map(fn(EventTheme $theme): string => $theme->name, $themes));
        self::assertCount(0, $eventThemeRepository->getByThemesOrderedByPriority(99));

        // L'identifiant est réinitialisé par Doctrine après la suppression, on le récupère avant
        $lightningThemeId = $themeLightning->id;
        $eventThemeRepository->delete($themeLightning);
        self::assertNull($eventThemeRepository->find($lightningThemeId));
        self::assertCount(2, $eventThemeRepository->getByThemesOrderedByPriority(42));
    }

    private function buildEventTheme(int $idForum, string $name, int $priority, ?string $description): EventTheme
    {
        $eventTheme = new EventTheme();
        $eventTheme->idForum = $idForum;
        $eventTheme->name = $name;
        $eventTheme->description = $description;
        $eventTheme->priority = $priority;

        return $eventTheme;
    }
}
