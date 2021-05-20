<?php

namespace AppBundle\Controller;

use Afup\Site\Corporate\Articles;
use Afup\Site\Corporate\Branche;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Talk;
use AppBundle\Site\Model\Feuille;

class HomeController extends SiteBaseController
{
    const MAX_ARTICLES = 5;
    const MAX_MEETUPS = 10;

    public function displayAction()
    {
        $afupBdd = $GLOBALS['AFUP_DB'];

        $articles = new Articles();
        $derniers_articles = $articles->chargerDerniersAjouts(self::MAX_ARTICLES);

        $branche = new Branche($afupBdd);
        $enfants = $branche->feuillesEnfants(Feuille::ID_FEUILLE_COLONNE_DROITE);

        $premiereFeuille = array_shift($enfants);
        $deuxiemeFeuille = array_shift($enfants);

        return $this->render(
            ':site:home.html.twig',
            [
                'actualites' => $derniers_articles,
                'meetups' => $this->getLatestMeetups(),
                'premiere_feuille' => $premiereFeuille,
                'deuxieme_feuille' => $deuxiemeFeuille,
                'autres_feuilles' => $enfants,
                'talk' => null === $deuxiemeFeuille ? $this->getTalkOfTheDay() : $deuxiemeFeuille,
            ]
        );
    }

    /**
     * @return Talk
     */
    protected function getTalkOfTheDay()
    {
        return $this->get('ting')->get(TalkRepository::class)->getTalkOfTheDay(new \DateTime());
    }

    /**
     * @return array
     */
    protected function getLatestMeetups()
    {
        if (false === $this->getParameter('home_algolia_enabled')) {
            return [];
        }

        $cache = $this->get('cache.system');
        $cacheKey = 'home_algolia_meetups';

        try {
            $cacheItem = $cache->getItem($cacheKey);
            if (!$cacheItem->isHit()) {
                $cacheItem->expiresAfter(new \DateInterval('P1D'));
                $cacheItem->set($this->doGetLatestMeetups());
                $cache->save($cacheItem);
            }

            return $cacheItem->get();
        } catch (\AlgoliaSearch\AlgoliaException $e) {
            $this->get('logger')->error($e->getMessage());
            return [];
        }
    }

    private function doGetLatestMeetups()
    {
        $algolia = $this->get(\AlgoliaSearch\Client::class);
        $index = $algolia->initIndex('afup_meetups');
        $results = $index->search('', ['hitsPerPage' => self::MAX_MEETUPS]);

        return $results['hits'];
    }
}
