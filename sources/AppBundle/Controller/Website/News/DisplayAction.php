<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\News;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Site\ArticleImageStorage;
use AppBundle\Site\Entity\Article;
use AppBundle\Site\Entity\Repository\ArticleRepository;
use AppBundle\Site\Enum\ArticleEtat;
use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class DisplayAction extends AbstractController
{
    public function __construct(
        private readonly ViewRenderer $view,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
        private readonly EventRepository $eventRepository,
        private readonly ArticleRepository $articleRepository,
        private readonly ArticleImageStorage $articleImageStorage,
    ) {}

    public function __invoke(string $code): Response
    {
        $article = $this->articleRepository->findArticleBySlug($code);
        if (null === $article) {
            throw $this->createNotFoundException();
        }

        if (!$this->authorizationChecker->isGranted('ROLE_ADMIN') && ($article->etat !== ArticleEtat::EnLigne || $article->datePublication > new \DateTime())) {
            throw $this->createNotFoundException();
        }

        return $this->view->render('site/news/display.html.twig', [
            'article' => $article,
            'previous' => $this->articleRepository->findPrevious($article),
            'next' => $this->articleRepository->findNext($article),
            'related_event' => $this->getRelatedEvent($article),
            'image_url' => $this->articleImageStorage->getUrl($article),
        ]);
    }

    private function getRelatedEvent(Article $article): ?Event
    {
        if (null === $article->idEvent) {
            return null;
        }

        return $this->eventRepository->get($article->idEvent);
    }
}
