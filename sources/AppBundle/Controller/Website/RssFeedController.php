<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website;

use AppBundle\Site\Entity\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class RssFeedController extends AbstractController
{
    public function __construct(private readonly ArticleRepository $articleRepository) {}

    public function __invoke(): Response
    {
        $articles = $this->articleRepository->findPublishedArticles(1, 20, []);
        $derniersArticles = [];
        foreach ($articles as $article) {
            $derniersArticles[] = [
                'titre'   => $article->titre,
                'contenu' => $article->getContenuFormate(),
                'url'     => $article->getSlug(),
                'maj'     => $article->datePublication->format(DATE_RSS),
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
                'lastBuildDate' => $derniersArticles[0]['maj'] ?? date(DATE_RSS, time()),
            ],
            'billets' => $derniersArticles,
        ];
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml; charset=UTF-8');
        return $this->render('site/rss.xml.twig', $datas, $response);
    }
}
