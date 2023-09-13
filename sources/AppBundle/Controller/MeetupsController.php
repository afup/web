<?php

namespace AppBundle\Controller;

use AppBundle\Event\Model\Repository\MeetupRepository;
use AppBundle\Indexation\Meetups\MeetupScraper;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * @throws Exception
     *
     * @return Response
     */
    public function scrapMeetupAction()
    {
        try {
            $meetups = $this->getScrapper()->getEvents();

            $meetupRepository = $this->get('ting')->get(MeetupRepository::class);

            foreach ($meetups as $antenneMeetups) {
                foreach ($antenneMeetups as $meetup) {
                    $existingMeetup = $meetupRepository->get($meetup->getId());
                    if (!$existingMeetup) {
                        $meetupRepository->save($meetup);
                    }
                }
            }
            //TODO: à laisser ?
            return new Response('Meetups scraped and saved successfully!');
        } catch (\Exception $e) {
            throw new \Exception('Problème lors du scraping ou de la sauvegarde des évènements Meetup', $e->getCode(), $e);
        }
    }

    /**
     * @return MeetupScraper
     */
    private function getScrapper()
    {
        return new MeetupScraper();
    }
}
