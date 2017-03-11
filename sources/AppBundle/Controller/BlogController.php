<?php


namespace AppBundle\Controller;

use AppBundle\Event\Model\Repository\TalkRepository;
use Symfony\Component\HttpFoundation\Response;

class BlogController extends EventBaseController
{
    /**
     * @param $eventSlug
     * @return Response
     */
    public function programAction($eventSlug)
    {
        $event = $this->checkEventSlug($eventSlug);

        /**
         * @var $talkRepository TalkRepository
         */
        $talkRepository = $this->get('ting')->get(TalkRepository::class);
        $talks = $talkRepository->getByEventWithSpeakers($event);

        return $this->render(':blog:program.html.twig', ['talks' => $talks, 'event' => $event]);
    }
}
