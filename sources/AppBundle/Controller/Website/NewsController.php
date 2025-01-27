<?php

namespace AppBundle\Controller\Website;

use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Site\Form\NewsFiltersType;
use AppBundle\Site\Model\Article;
use AppBundle\Site\Model\Repository\ArticleRepository;
use AppBundle\WebsiteBlocks;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class NewsController extends Controller
{
    const ARTICLES_PER_PAGE = 5;

    private WebsiteBlocks $websiteBlocks;

    public function __construct(WebsiteBlocks $websiteBlocks)
    {
        $this->websiteBlocks = $websiteBlocks;
    }

    public function displayAction($code)
    {
        $articleRepository = $this->getArticleRepository();

        $article = $articleRepository->findNewsBySlug($code);
        if (null === $article) {
            throw $this->createNotFoundException();
        }

        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            if ($article->getState() === 0 || !($article->getPublishedAt() <= new \DateTime())) {
                throw $this->createNotFoundException();
            }
        }

        $this->getHeaderImageUrl($article);

        return $this->websiteBlocks->render('site/news/display.html.twig', [
            'article' => $article,
            'header_image' => $this->getHeaderImageUrl($article),
            'previous' => $articleRepository->findPrevious($article),
            'next' => $articleRepository->findNext($article),
            'related_event' => $this->getRelatedEvent($article),
        ]);
    }

    private function getRelatedEvent(Article $article)
    {
        if (null === ($eventId = $article->getEventId())) {
            return null;
        }

        return $this->get('ting')->get(EventRepository::class)->get($eventId);
    }

    private function getHeaderImageUrl(Article $article)
    {
        if (null === ($theme = $article->getTheme())) {
            return null;
        }

        $image = '/images/news/' . $theme . '.png';

        $url = $this->getParameter('kernel.project_dir') . '/htdocs' . $image ;

        if (false === is_file($url)) {
            return null;
        }

        return $image;
    }

    public function listAction(Request $request)
    {
        $page = $request->get('page', 1);

        $form = $this->createForm(NewsFiltersType::class);
        $form->handleRequest($request);

        $formData = $form->getData();
        $filters = $formData ?? [];

        return $this->websiteBlocks->render('site/news/list.html.twig', [
            'filters' => $filters,
            'articles' => $this->getArticleRepository()->findPublishedNews($page, self::ARTICLES_PER_PAGE, $filters),
            'total_items' => $this->getArticleRepository()->countPublishedNews($filters),
            'current_page' => $page,
            'articles_per_page' => self::ARTICLES_PER_PAGE,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return ArticleRepository
     */
    private function getArticleRepository()
    {
        return $this->get('ting')->get(ArticleRepository::class);
    }
}
