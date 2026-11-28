<?php

declare(strict_types=1);

namespace AppBundle\Tests\Indexation\Talks;

use Algolia\AlgoliaSearch\SearchClient;
use Algolia\AlgoliaSearch\SearchIndex;
use AppBundle\Event\Entity\Planning;
use AppBundle\Event\Entity\Repository\PlanningRepository;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Entity\Repository\SpeakerRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Speaker\SpeakerProfileResolver;
use AppBundle\Event\Talk\PublishedTalkRepository;
use AppBundle\Indexation\Talks\Runner;
use PHPUnit\Framework\TestCase;

final class RunnerTest extends TestCase
{
    public function testOnlyPublishedTalksAreIndexed(): void
    {
        $index = $this->createMock(SearchIndex::class);
        $index->expects(self::once())->method('saveObjects')->with([['talk_id' => 1], ['talk_id' => 3]]);

        $publishedTalkRepository = $this->createStub(PublishedTalkRepository::class);
        $publishedTalkRepository->method('getIds')->willReturn([1, 3]);

        // Dépôt final, jamais appelé : getAllPlannings() est redéfinie.
        $runner = new class ($index, $publishedTalkRepository, $this->createStub(SearchClient::class), new \ReflectionClass(PlanningRepository::class)->newInstanceWithoutConstructor(), $this->createStub(TalkRepository::class), $this->createStub(EventRepository::class), $this->createStub(SpeakerRepository::class)) extends Runner {
            public function __construct(
                private readonly SearchIndex $index,
                PublishedTalkRepository $publishedTalkRepository,
                SearchClient $client,
                PlanningRepository $plannings,
                TalkRepository $talks,
                EventRepository $events,
                SpeakerRepository $speakers,
            ) {
                parent::__construct($client, $plannings, $talks, $events, $speakers, $publishedTalkRepository, new SpeakerProfileResolver($publishedTalkRepository, $speakers));
            }

            protected function initIndex(): SearchIndex
            {
                return $this->index;
            }

            protected function getAllPlannings(): array
            {
                return array_map(static function (int $talkId): Planning {
                    $planning = new Planning();
                    $planning->talkId = $talkId;

                    return $planning;
                }, [1, 2, 3]);
            }

            protected function prepareObject(Planning $planning): ?array
            {
                return ['talk_id' => $planning->talkId];
            }
        };

        $runner->run();
    }
}
