<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website;

use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Site\Form\NewsFiltersType;
use AppBundle\Site\Model\Article;
use AppBundle\Site\Model\Repository\ArticleRepository;
use AppBundle\Twig\ViewRenderer;
use CCMBenchmark\TingBundle\Repository\RepositoryFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class NewsController extends AbstractController
{
    public const ARTICLES_PER_PAGE = 5;

    public function __construct(
        private readonly ViewRenderer $view,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
        private readonly RepositoryFactory $repositoryFactory,
        private readonly string $projectDir,
    ) {
    }

    public function display($code): Response
    {
        $articleRepository = $this->getArticleRepository();

        $article = $articleRepository->findNewsBySlug($code);
        if (null === $article) {
            throw $this->createNotFoundException();
        }

        if (!$this->authorizationChecker->isGranted('ROLE_ADMIN') && ($article->getState() === 0 || $article->getPublishedAt() > new \DateTime())) {
            throw $this->createNotFoundException();
        }

        $this->getHeaderImageUrl($article);

        return $this->view->render('site/news/display.html.twig', [
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

        return $this->repositoryFactory->get(EventRepository::class)->get($eventId);
    }

    private function getHeaderImageUrl(Article $article): ?string
    {
        if (null === ($theme = $article->getTheme())) {
            return null;
        }

        $image = '/images/news/' . $theme . '.png';

        $url = $this->projectDir . '/htdocs' . $image ;

        if (false === is_file($url)) {
            return null;
        }

        return $image;
    }

    public function list(Request $request): Response
    {
        $page = $request->get('page', 1);

        $form = $this->createForm(NewsFiltersType::class);
        $form->handleRequest($request);

        $formData = $form->getData();
        $filters = $formData ?? [];

        return $this->view->render('site/news/list.html.twig', [
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
        return $this->repositoryFactory->get(ArticleRepository::class);
    }
}
