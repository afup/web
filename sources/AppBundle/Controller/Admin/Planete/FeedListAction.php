<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Planete;

use Exception;
use PlanetePHP\Feed;
use PlanetePHP\FeedRepository;
use SimpleXMLElement;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class FeedListAction
{
    public function __construct(
        private readonly FeedRepository $feedRepository,
        private readonly Environment $twig,
    ) {
    }

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
        // on n'affiche rien du tout
        ini_set('display_errors', '0');
        set_time_limit(240);
        $results = [];
        foreach ($feeds as $f) {
            if ($f->getStatus()) {
                try {
                    new SimpleXmlElement(file_get_contents($f->getFeed()));
                    $results[$f->getId()] = true;
                } catch (Exception) {
                    $results[$f->getId()] = false;
                }
            }
        }

        return $results;
    }
}
