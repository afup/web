<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Speaker;

use Afup\Site\Logger\DbLoggerTrait;
use Afup\Site\Utils\Utils;
use AppBundle\CFP\PhotoStorage;
use AppBundle\Event\Form\SpeakerFormDataFactory;
use AppBundle\Event\Form\SpeakerType;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Speaker;
use AppBundle\Event\Model\Talk;
use Assert\Assertion;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class SpeakerEditAction extends AbstractController
{
    use DbLoggerTrait;

    const ID_FORUM_PHOTO_STORAGE = 16;
    private SpeakerRepository $speakerRepository;
    private TalkRepository $talkRepository;
    private EventRepository $eventRepository;
    private PhotoStorage $photoStorage;
    private SpeakerFormDataFactory $speakerFormDataFactory;

    public function __construct(
        SpeakerRepository $speakerRepository,
        TalkRepository $talkRepository,
        EventRepository $eventRepository,
        PhotoStorage $photoStorage,
        SpeakerFormDataFactory $speakerFormDataFactory
    ) {
        $this->speakerRepository = $speakerRepository;
        $this->talkRepository = $talkRepository;
        $this->eventRepository = $eventRepository;
        $this->photoStorage = $photoStorage;
        $this->speakerFormDataFactory = $speakerFormDataFactory;
    }

    public function __invoke(Request $request)
    {
        /** @var Speaker $speaker */
        $speaker = $this->speakerRepository->get($request->query->get('id'));
        Assertion::notNull($speaker);
        /** @var Event $event */
        $event = $this->eventRepository->get($speaker->getEventId());
        Assertion::notNull($event);
        $data = $this->speakerFormDataFactory->fromSpeaker($speaker);
        $form = $this->createForm(SpeakerType::class, $data, [
            SpeakerType::OPT_PHOTO_REQUIRED => null === $speaker->getPhoto(),
            SpeakerType::OPT_USER_GITHUB => true,
        ]);
        $talks = [];
        foreach ($this->talkRepository->getTalksBySpeaker($event, $speaker) as $talk) {
            if ($talk->getType() !== Talk::TYPE_PHP_PROJECT) {
                $talks[$talk->getTitle()] = $talk;
            }
        }
        ksort($talks);
        $talks = array_values($talks);
        $photo = $originalPhoto = null;
        if (null !== $speaker->getPhoto()) {
            $photo = $this->photoStorage->getUrl($speaker, PhotoStorage::DIR_THUMBS);
            $originalPhoto = $this->photoStorage->getUrl($speaker, PhotoStorage::DIR_ORIGINAL);
            if (null === $photo && $event->getId() < self::ID_FORUM_PHOTO_STORAGE) {
                $photo = $this->photoStorage->getLegacyUrl($event, $speaker);
            }
        }
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $speaker->setCivility($data->civility);
            $speaker->setFirstname($data->firstname);
            $speaker->setLastname($data->lastname);
            $speaker->setBiography($data->biography);
            $speaker->setTwitter($data->twitter);
            $speaker->setBluesky($data->bluesky);
            $speaker->setMastodon($data->mastodon);
            $speaker->setEmail($data->email);
            $speaker->setUser($data->githubUser !== null ? $data->githubUser->getId() : null);
            $speaker->setCompany($data->company);
            $speaker->setLocality($data->locality);
            $speaker->setPhoneNumber($data->phoneNumber);
            $speaker->setReferentPerson($data->referentPerson);
            $speaker->setReferentPersonEmail($data->referentPersonEmail);
            $this->speakerRepository->save($speaker);
            if ($data->photoFile) {
                if ($event->getId() < self::ID_FORUM_PHOTO_STORAGE) {
                    $fileName = $this->photoStorage->storeLegacy($data->photoFile, $event, $speaker);
                } else {
                    $fileName = $this->photoStorage->store($data->photoFile, $speaker);
                }
                $speaker->setPhoto($fileName);
            }
            $this->speakerRepository->save($speaker);
            $this->log('Modification du conférencier de ' . $speaker->getFirstname() . ' ' . $speaker->getLastname() . ' (' . $speaker->getId() . ')');

            $this->addFlash('notice', 'Le conférencier a été modifié');

            return $this->redirectToRoute('admin_speaker_list', [
                'eventId' => $event->getId()
            ]);
        }

        return $this->render('admin/speaker/edit.html.twig', [
            'speakerId' => $speaker->getId(),
            'eventId' => $event->getId(),
            'gravatar' => Utils::get_gravatar($speaker->getEmail(), 90),
            'eventTitle' => $event->getTitle(),
            'form' => $form->createView(),
            'talks' => $talks,
            'photo' => $photo,
            'originalPhoto' => $originalPhoto,
        ]);
    }
}
