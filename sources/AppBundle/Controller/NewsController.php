<?php

namespace AppBundle\Controller;

use AppBundle\Site\Form\NewsFiltersType;
use AppBundle\Site\Model\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;

class NewsController extends SiteBaseController
{
    const ARTICLES_PER_PAGE = 5;

    public function displayAction($code)
    {
        $article = $this->getArticleRepository()->findNewsBySlug($code);

        if (null === $article) {
            throw $this->createNotFoundException();
        }

        if (!($article->getPublishedAt() <= new \DateTime())) {
            throw $this->createNotFoundException();
        }

        return $this->render(
            ':site:news/display.html.twig',
            [
                'article' => $article,
            ]
        );
    }

    public function listAction(Request $request)
    {
        $page = $request->get('page', 1);

        $form = $this->createForm(NewsFiltersType::class);
        $form->handleRequest($request);

        $formData = $form->getData();
        $filters = null === $formData ? [] : $formData;

        return $this->render(
            ':site:news/list.html.twig',
            [
                'filters' => $filters,
                'articles' => $this->getArticleRepository()->findPublishedNews($page, self::ARTICLES_PER_PAGE, $filters),
                'total_items' => $this->getArticleRepository()->countPublishedNews($filters),
                'current_page' => $page,
                'articles_per_page' => self::ARTICLES_PER_PAGE,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @return ArticleRepository
     */
    private function getArticleRepository()
    {
        return $this->get('ting')->get(ArticleRepository::class);
    }
}
