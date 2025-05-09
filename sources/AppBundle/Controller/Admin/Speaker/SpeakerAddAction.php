<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Speaker;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\CFP\PhotoStorage;
use AppBundle\Event\Form\SpeakerFormData;
use AppBundle\Event\Form\SpeakerType;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Speaker;
use Assert\Assertion;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SpeakerAddAction extends AbstractController
{
    use DbLoggerTrait;

    public function __construct(
        private EventRepository $eventRepository,
        private SpeakerRepository $speakerRepository,
        private PhotoStorage $photoStorage,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        /** @var Event $event */
        $event = $this->eventRepository->get($request->query->get('eventId'));
        Assertion::notNull($event);
        $data = new SpeakerFormData();
        $form = $this->createForm(SpeakerType::class, $data, [
            SpeakerType::OPT_USER_GITHUB => true,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $speaker = new Speaker();
            $speaker->setEventId($event->getId());
            $speaker->setCivility($data->civility);
            $speaker->setFirstname($data->firstname);
            $speaker->setLastname($data->lastname);
            $speaker->setBiography($data->biography);
            $speaker->setTwitter($data->twitter);
            $speaker->setMastodon($data->mastodon);
            $speaker->setBluesky($data->bluesky);
            $speaker->setEmail($data->email);
            $speaker->setUser($data->githubUser !== null ? $data->githubUser->getId() : null);
            $speaker->setCompany($data->company);
            $speaker->setPhoneNumber($data->phoneNumber);
            $speaker->setReferentPerson($data->referentPerson);
            $this->speakerRepository->save($speaker);
            if (null !== $data->photoFile) {
                $fileName = $this->photoStorage->store($data->photoFile, $speaker);
                $speaker->setPhoto($fileName);
                $this->speakerRepository->save($speaker);
            }
            $this->log('Ajout du conférencier de ' . $speaker->getFirstname() . ' ' . $speaker->getLastname());

            $this->addFlash('notice', 'Le conférencier a été ajouté');

            return $this->redirectToRoute('admin_speaker_list', [
                'eventId' => $event->getId(),
            ]);
        }

        return $this->render('admin/speaker/add.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }
}
