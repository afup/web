<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website;

use AppBundle\Site\Model\Article;
use AppBundle\Site\Model\Repository\ArticleRepository;
use CCMBenchmark\TingBundle\Repository\RepositoryFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class RssFeedController extends AbstractController
{
    public function __construct(private readonly RepositoryFactory $repositoryFactory)
    {
    }
    public function __invoke(): Response
    {
        $articles = $this->repositoryFactory->get(ArticleRepository::class)->findPublishedNews(1, 20, []);
        $derniersArticles = [];
        foreach ($articles as $article) {
            /** @var Article $article */
            $derniersArticles[] = [
                'titre'   => $article->getTitle(),
                'contenu' => $article->getContent(),
                'url'     => $article->getSlug(),
                'maj'     => $article->getPublishedAt()->format(DATE_RSS),
            ];
        }
        $datas = [
            'feed' => [
            'title'         => "Le flux RSS de l'AFUP",
            'url'           => 'https://afup.org/',
            'link'          => 'https://afup.org/rss.xml',
            'email'         => 'bonjour@afup.org',
            'author'        => 'AFUP',
            'date'          => date(DATE_RSS),
            'lastBuildDate' =>
                $derniersArticles[0]['maj'] ?? date(DATE_RSS, time()),
            ],
            'billets' => $derniersArticles,
        ];
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml; charset=UTF-8');
        return $this->render('site/rss.xml.twig', $datas, $response);
    }
}
