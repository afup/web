<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Global;

use Afup\Site\Corporate\Feuille;
use Algolia\AlgoliaSearch\Exceptions\AlgoliaException;
use Algolia\AlgoliaSearch\SearchClient;
use AppBundle\Event\Model\Meetup;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Talk;
use AppBundle\Site\Model\Repository\ArticleRepository;
use AppBundle\Site\Model\Repository\SheetRepository;
use AppBundle\Twig\ViewRenderer;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;

final class HomeAction extends AbstractController
{
    public const MAX_MEETUPS = 10;

    public function __construct(
        private readonly ViewRenderer $view,
        private readonly LoggerInterface $logger,
        private readonly CacheItemPoolInterface $cache,
        private readonly SearchClient $client,
        private readonly TalkRepository $talkRepository,
        #[Autowire('%env(HOME_ALGOLIA_ENABLED)%')]
        private readonly bool $homeAlgoliaEnabled,
        private readonly ArticleRepository $articleRepository,
        private readonly SheetRepository $sheetRepository,
    ) {}

    public function __invoke(): Response
    {
        $derniers_articles = $this->articleRepository->findListForHome();

        $enfants = iterator_to_array($this->sheetRepository->getActiveChildrenByParentId(Feuille::ID_FEUILLE_COLONNE_DROITE));

        $premiereFeuille = array_shift($enfants);
        $deuxiemeFeuille = array_shift($enfants);

        return $this->view->render('site/home.html.twig', [
            'actualites' => $derniers_articles,
            'meetups' => $this->getLatestMeetups(),
            'premiere_feuille' => $premiereFeuille,
            'deuxieme_feuille' => $deuxiemeFeuille,
            'autres_feuilles' => $enfants,
            'talk' => $deuxiemeFeuille ?? $this->getTalkOfTheDay(),
        ]);
    }

    protected function getTalkOfTheDay(): Talk
    {
        return $this->talkRepository->getTalkOfTheDay(new \DateTime());
    }

    /**
     * @return array<Meetup>
     */
    protected function getLatestMeetups(): array
    {
        if (!$this->homeAlgoliaEnabled) {
            return [];
        }

        $cacheKey = 'home_algolia_meetups';

        try {
            $cacheItem = $this->cache->getItem($cacheKey);
            if (!$cacheItem->isHit()) {
                $cacheItem->expiresAfter(new \DateInterval('P1D'));
                $cacheItem->set($this->doGetLatestMeetups());
                $this->cache->save($cacheItem);
            }

            return $cacheItem->get();
        } catch (AlgoliaException|InvalidArgumentException $e) {
            $this->logger->error($e->getMessage());
            return [];
        }
    }

    private function doGetLatestMeetups()
    {
        $algolia = $this->client;
        $index = $algolia->initIndex('afup_meetups');
        $results = $index->search('', ['hitsPerPage' => self::MAX_MEETUPS]);

        return $results['hits'];
    }
}
