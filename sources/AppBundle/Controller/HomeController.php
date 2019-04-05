<?php

namespace AppBundle\Controller;

use Afup\Site\Corporate\Articles;
use Afup\Site\Corporate\Branche;
use Afup\Site\Corporate\Feuille;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Talk;

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

        $algolia = $this->get(\AlgoliaSearch\Client::class);
        $index = $algolia->initIndex('afup_meetups');
        $results = $index->search('', ['hitsPerPage' => self::MAX_MEETUPS]);

        return $results['hits'];
    }
}
