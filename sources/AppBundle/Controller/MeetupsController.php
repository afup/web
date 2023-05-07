<?php

namespace AppBundle\Controller;

use AppBundle\Controller\SiteBaseController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Indexation\Meetup\MeetupScraper;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Indexation\Meetup\Repository\MeetupRepository;

class MeetupsController extends SiteBaseController
{
    public function listAction(Request $request)
    {
        return $this->render(
            ':site:meetups/list.html.twig',
            [
                'algolia_app_id' => $this->getParameter('algolia_app_id'),
                'algolia_api_key' => $this->getParameter('algolia_frontend_api_key'),
                'source' => $request->get('src'),
            ]
        );
    }

    /**
     * @param string $antenne;
     * @return Response
     */
    public function scrapMeetupAction($antenne){
        $antenne = mb_strtolower($antenne);
        
        $meetups = $this->getScrapper()->getEvents($antenne);

        $meetupRepository = $this->getMeetupRepository();
        foreach ($meetups as $meetup) {
            $meetupRepository->saveMeetup($meetup, $antenne);
        }

        return new Response();
    }

    /**
     * @return MeetupScraper
     */
    private function getScrapper()
    {
        return  new MeetupScraper();
    }

    /**
     * @return MeetupRepository
     */
    private function getMeetupRepository()
    {
        return $this->get('ting')->get(MeetupRepository::class);
    }
}
