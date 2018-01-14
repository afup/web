<?php

namespace AppBundle\Controller;

use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\PlanningRepository;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Repository\TalkRepository;

class TalksController extends SiteBaseController
{
    public function listAction()
    {
        return $this->render(
            ':site:talks/list.html.twig',
            [
                'algolia_app_id' => $this->getParameter('algolia_app_id'),
                'algolia_api_key' => $this->getParameter('algolia_frontend_api_key'),
            ]
        );
    }

    /**
     * @param integer $id
     * @param string $slug
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($id, $slug)
    {
        $talk = $this->get('ting')->get(TalkRepository::class)->get($id);

        if (null === $talk || $talk->getSlug() != $slug || !$talk->isDisplayedOnHistory()) {
            throw $this->createNotFoundException();
        }

        $speakers = $this->get('ting')->get(SpeakerRepository::class)->getSpeakersByTalk($talk);
        $planning = $this->get('ting')->get(PlanningRepository::class)->getByTalk($talk);
        $event = $this->get('ting')->get(EventRepository::class)->get($planning->getEventId());
        $comments = $this->get('app.joindin.comments')->getCommentsFromTalk($talk);

        return $this->render(
            ':site:talks/show.html.twig',
            [
                'talk' => $talk,
                'event' => $event,
                'speakers' => $speakers,
                'comments' => $comments,
            ]
        );
    }
}
