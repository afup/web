<?php

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Form\EventSelectType;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class SpeakersManagementAction extends Controller
{
    /** @var EventActionHelper */
    private $eventActionHelper;
    /** @var SpeakerRepository */
    private $speakerRepository;
    /** @var FormFactoryInterface */
    private $formFactory;
    /** @var Environment */
    private $twig;

    public function __construct(
        EventActionHelper $eventActionHelper,
        SpeakerRepository $speakerRepository,
        FormFactoryInterface $formFactory,
        Environment $twig
    ) {
        $this->eventActionHelper = $eventActionHelper;
        $this->speakerRepository = $speakerRepository;
        $this->twig = $twig;
        $this->formFactory = $formFactory;
    }

    public function __invoke(Request $request)
    {
        $id = $request->query->get('id');

        $event = $this->eventActionHelper->getEventById($id);

        return new Response($this->twig->render('admin/event/speakers_management.html.twig', [
            'event' => $event,
            'title' => 'Gestion documentaire des speakers',
            'speakers' => $event === null ? null : $this->speakerRepository->getScheduledSpeakersByEvent($event, true),
            'event_select_form' => $this->formFactory->create(EventSelectType::class, $event)->createView(),
        ]));
    }
}
