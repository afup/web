<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests\Event\Entity\Repository;

use Afup\Tests\Support\IntegrationTestCase;
use AppBundle\Antennes\AntenneRepository;
use AppBundle\Event\Entity\Meetup;
use AppBundle\Event\Entity\Repository\MeetupRepository;

final class MeetupRepositoryTest extends IntegrationTestCase
{
    public function testFindSaveAndQueries(): void
    {
        $meetupRepository = self::getContainer()->get(MeetupRepository::class);
        $antennesCollection = self::getContainer()->get(AntenneRepository::class);
        $antenne = $antennesCollection->findByCode('lyon');

        self::assertSame([], $meetupRepository->findAllForAntenne($antenne));
        self::assertNull($meetupRepository->findNextForAntenne($antenne));
        self::assertSame([], $meetupRepository->findNextEvents(3));

        $passe = new Meetup();
        $passe->id = 1;
        $passe->date = new \DateTimeImmutable('yesterday');
        $passe->titre = 'Meetup passé';
        $passe->lieu = 'Lyon';
        $passe->description = 'Meetup à Lyon';
        $passe->codeAntenne = $antenne->code;
        $passe->photoUrl = null;
        $meetupRepository->save($passe);

        $futur = new Meetup();
        $futur->id = 2;
        $futur->date = new \DateTimeImmutable('tomorrow');
        $futur->titre = 'Meetup futur';
        $futur->lieu = 'Lyon';
        $futur->description = 'Meetup à Lyon';
        $futur->codeAntenne = $antenne->code;
        $futur->photoUrl = 'https://example.com/photo.png';
        $meetupRepository->save($futur);

        $autreAntenne = new Meetup();
        $autreAntenne->id = 3;
        $autreAntenne->date = new \DateTimeImmutable('+2 days');
        $autreAntenne->titre = 'Meetup autre antenne';
        $autreAntenne->lieu = 'Paris';
        $autreAntenne->description = 'Meetup à Paris';
        $autreAntenne->codeAntenne = 'paris';
        $autreAntenne->photoUrl = null;
        $meetupRepository->save($autreAntenne);

        $next = $meetupRepository->findNextForAntenne($antenne);
        self::assertNotNull($next);
        self::assertSame(2, $next->id);

        $all = $meetupRepository->findAllForAntenne($antenne);
        self::assertCount(2, $all);
        self::assertSame(['Meetup passé', 'Meetup futur'], array_map(
            static fn(Meetup $meetup): string => $meetup->titre,
            $all,
        ));

        self::assertSame([3, 2], array_map(
            static fn(Meetup $meetup): int => $meetup->id,
            $meetupRepository->findNextEvents(2),
        ));

        // Mise à jour d'une entité existante proche du comportement de ScrappingMeetupEventsCommand
        $existing = $meetupRepository->find(2);
        self::assertNotNull($existing);
        $existing->titre = 'Meetup futur mis à jour';
        $meetupRepository->save($existing);
        self::assertSame('Meetup futur mis à jour', $meetupRepository->find(2)?->titre);
    }
}
