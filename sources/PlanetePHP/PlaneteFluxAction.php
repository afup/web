<?php

namespace PlanetePHP;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class PlaneteFluxAction
{
    /** @var FeedArticleRepository */
    private $feedArticleRepository;
    /** @var UrlGeneratorInterface */
    private $urlGenerator;
    /** @var Environment */
    private $twig;

    public function __construct(
        FeedArticleRepository $feedArticleRepository,
        UrlGeneratorInterface $urlGenerator,
        Environment $twig
    ) {
        $this->feedArticleRepository = $feedArticleRepository;
        $this->urlGenerator = $urlGenerator;
        $this->twig = $twig;
    }

    public function __invoke(Request $request)
    {
        $articles = $this->feedArticleRepository->findLatest(0, DATE_ATOM, 20);
        foreach ($articles as $article) {
            $article->setContent(htmlspecialchars(html_entity_decode(strip_tags($article->getContent()))));
        }

        $response = new Response($this->twig->render('flux.xml.twig', [
            'feed' => [
                'title' => 'planete php fr',
                'url' => $this->urlGenerator->generate('planete', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'link' => $this->urlGenerator->generate('planete_flux', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'email' => 'planetephpfr@afup.org',
                'author' => 'Perrick Penet / AFUP',
                'date' => date(DATE_ATOM),
            ],
            'articles' => $articles,
        ]));
        $response->headers->set('Content-Type', 'application/atom+xml; charset=UTF-8');

        return $response;
    }
}
