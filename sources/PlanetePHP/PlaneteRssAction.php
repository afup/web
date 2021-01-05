<?php

namespace PlanetePHP;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class PlaneteRssAction
{
    /** @var FeedArticleRepository */
    private $planeteBillet;
    /** @var Environment */
    private $twig;

    public function __construct(
        FeedArticleRepository $planeteBillet,
        Environment $twig
    ) {
        $this->planeteBillet = $planeteBillet;
        $this->twig = $twig;
    }

    public function __invoke(Request $request)
    {
        $response = new Response($this->twig->render('rss.xml.twig', [
            'feed' => [
                'title' => 'planete php fr',
                'url' => 'http://planete-php.fr/',
                'link' => 'http://planete-php.fr/rss.php',
                'email' => 'planetephpfr@afup.org',
                'author' => 'Perrick Penet / AFUP',
                'date' => date(DATE_RSS),
            ],
            'articles' => $this->planeteBillet->findLatest(0, DATE_RSS, 20),
        ]));
        $response->headers->set('Content-Type', 'text/xml; charset=UTF-8');

        return $response;
    }
}
