<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website;

use Afup\Site\Corporate\Articles;
use Afup\Site\Corporate\Branche;
use Afup\Site\Corporate\Feuille;
use AlgoliaSearch\AlgoliaException;
use AlgoliaSearch\Client;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Talk;
use AppBundle\Twig\ViewRenderer;
use CCMBenchmark\TingBundle\Repository\RepositoryFactory;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
    public const MAX_ARTICLES = 5;
    public const MAX_MEETUPS = 10;
    private ViewRenderer $view;
    private LoggerInterface $logger;
    private RepositoryFactory $repositoryFactory;
    private AdapterInterface $traceableAdapter;
    private Client $client;
    private bool $homeAlgoliaEnabled;

    public function __construct(ViewRenderer $view,
                                LoggerInterface $logger,
                                RepositoryFactory $repositoryFactory,
                                AdapterInterface $traceableAdapter,
                                Client $client,
                                bool $homeAlgoliaEnabled)
    {
        $this->view = $view;
        $this->logger = $logger;
        $this->repositoryFactory = $repositoryFactory;
        $this->traceableAdapter = $traceableAdapter;
        $this->client = $client;
        $this->homeAlgoliaEnabled = $homeAlgoliaEnabled;
    }

    public function display(): Response
    {
        $articles = new Articles();
        $derniers_articles = $articles->chargerDerniersAjouts(self::MAX_ARTICLES);

        $branche = new Branche();
        $enfants = $branche->feuillesEnfants(Feuille::ID_FEUILLE_COLONNE_DROITE);

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

    /**
     * @return Talk
     */
    protected function getTalkOfTheDay()
    {
        return $this->repositoryFactory->get(TalkRepository::class)->getTalkOfTheDay(new \DateTime());
    }

    /**
     * @return array
     */
    protected function getLatestMeetups()
    {
        if (!$this->homeAlgoliaEnabled) {
            return [];
        }

        $cache = $this->traceableAdapter;
        $cacheKey = 'home_algolia_meetups';

        try {
            $cacheItem = $cache->getItem($cacheKey);
            if (!$cacheItem->isHit()) {
                $cacheItem->expiresAfter(new \DateInterval('P1D'));
                $cacheItem->set($this->doGetLatestMeetups());
                $cache->save($cacheItem);
            }

            return $cacheItem->get();
        } catch (AlgoliaException $e) {
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
