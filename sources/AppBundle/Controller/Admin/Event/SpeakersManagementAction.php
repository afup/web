<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Form\EventSelectType;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\SpeakerInfos\SpeakersExpensesStorage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class SpeakersManagementAction extends AbstractController
{
    private EventActionHelper $eventActionHelper;
    private SpeakerRepository $speakerRepository;
    private FormFactoryInterface $formFactory;
    private Environment $twig;
    private SpeakersExpensesStorage $speakersExpensesStorage;

    public function __construct(
        EventActionHelper $eventActionHelper,
        SpeakerRepository $speakerRepository,
        FormFactoryInterface $formFactory,
        Environment $twig,
        SpeakersExpensesStorage $speakersExpensesStorage
    ) {
        $this->eventActionHelper = $eventActionHelper;
        $this->speakerRepository = $speakerRepository;
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->speakersExpensesStorage = $speakersExpensesStorage;
    }

    public function __invoke(Request $request)
    {
        $id = $request->query->get('id');

        $event = $this->eventActionHelper->getEventById($id);

        $speakers = $event === null ? null : $this->speakerRepository->getScheduledSpeakersByEvent($event, true);
        if (null !== $speakers) {
            $speakers = iterator_to_array($speakers->getIterator());
            foreach ($speakers as $k => $speaker) {
                $files = $this->speakersExpensesStorage->getFiles($speaker['speaker']);
                $speakers[$k]['hasExpensesFiles'] = count($files) >= 1;
            }
        }


        return new Response($this->twig->render('admin/event/speakers_management.html.twig', [
            'event' => $event,
            'speakers' => $speakers,
            'event_select_form' => $this->formFactory->create(EventSelectType::class, $event)->createView(),
        ]));
    }
}
