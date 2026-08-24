<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Global;

use AppBundle\Event\Entity\Repository\MeetupRepository;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Site\ArticleImageStorage;
use AppBundle\Site\Entity\Repository\ArticleRepository;
use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class HomeAction extends AbstractController
{
    public function __construct(
        private readonly ViewRenderer $view,
        private readonly ArticleRepository $articleRepository,
        private readonly MeetupRepository $meetupRepository,
        private readonly EventRepository $eventRepository,
        private readonly ArticleImageStorage $articleImageStorage,
    ) {}

    public function __invoke(): Response
    {
        $articles = $this->articleRepository->findListForHome();
        $meetups = $this->meetupRepository->findNextEvents(6);

        return $this->view->render('site/home.html.twig', [
            'articles' => $articles,
            'meetups' => $meetups,
            'lastArticleImageUrl' => $this->articleImageStorage->getUrl($articles[0]) ?? 'images/article-default-image.svg',
            'currentEvent' => $this->eventRepository->getMostRecentEvent(),
        ]);
    }
}
