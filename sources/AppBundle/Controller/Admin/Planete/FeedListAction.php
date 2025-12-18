<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Planete;

use PlanetePHP\Feed;
use PlanetePHP\FeedRepository;
use PlanetePHP\FeedStatus;
use PlanetePHP\FeedTester;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final readonly class FeedListAction
{
    public function __construct(
        private FeedRepository $feedRepository,
        private Environment $twig,
        private FeedTester $feedTester,
    ) {}

    public function __invoke(Request $request): Response
    {
        $testFeeds = $request->query->getBoolean('testFeeds');
        $filter = $request->query->get('filter');
        $sort = $request->query->get('sort', 'name');
        $direction = $request->query->get('direction', 'asc');
        $feeds = $this->feedRepository->find($sort, $direction, $filter);

        return new Response($this->twig->render('admin/planete/feed_list.html.twig', [
            'feeds' => $feeds,
            'testFeeds' => $testFeeds,
            'feedResults' => $testFeeds ? $this->testFeeds($feeds) : [],
            'sort' => $sort,
            'direction' => $direction,
            'filter' => $filter,
        ]));
    }

    /**
     * @param Feed[] $feeds
     *
     * @return array<int, bool>
     */
    private function testFeeds(array $feeds): array
    {
        $results = [];

        foreach ($feeds as $feed) {
            if ($feed->status === FeedStatus::Active) {
                $results[$feed->id] = $this->feedTester->test($feed);
            }
        }

        return $results;
    }
}
