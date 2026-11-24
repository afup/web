<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Speaker;

use Afup\Site\Utils\Utils;
use AppBundle\AuditLog\Audit;
use AppBundle\CFP\PhotoStorage;
use AppBundle\Event\Entity\GithubUser;
use AppBundle\Event\Entity\Repository\PersonneConferencierRepository;
use AppBundle\Event\Form\SpeakerFormDataFactory;
use AppBundle\Event\Form\SpeakerType;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Entity\Repository\SpeakerRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Talk;
use AppBundle\Event\Speaker\SpeakerSearchResult;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_EVENT_WRITER')]
class SpeakerEditAction extends AbstractController
{
    public const int ID_FORUM_PHOTO_STORAGE = 16;

    public function __construct(
        private readonly SpeakerRepository $speakerRepository,
        private readonly TalkRepository $talkRepository,
        private readonly EventRepository $eventRepository,
        private readonly PhotoStorage $photoStorage,
        private readonly SpeakerFormDataFactory $speakerFormDataFactory,
        private readonly PersonneConferencierRepository $personneConferencierRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request)
    {
        $speaker = $this->speakerRepository->find($request->query->getInt('id'));
        Assert::notNull($speaker);
        $event = $this->eventRepository->get($speaker->eventId);
        Assert::notNull($event);
        $data = $this->speakerFormDataFactory->fromSpeaker($speaker);
        $form = $this->createForm(SpeakerType::class, $data, [
            SpeakerType::OPT_PHOTO_REQUIRED => null === $speaker->photo,
            SpeakerType::OPT_USER_GITHUB => true,
        ]);
        $talks = [];
        foreach ($this->talkRepository->getTalksBySpeaker($event, $speaker->id) as $talk) {
            if ($talk->getType() !== Talk::TYPE_PHP_PROJECT) {
                $talks[$talk->getTitle()] = $talk;
            }
        }
        ksort($talks);
        $talks = array_values($talks);
        $photo = $originalPhoto = null;
        if (null !== $speaker->photo) {
            $photo = $this->photoStorage->getUrl($speaker, PhotoStorage::DIR_THUMBS);
            $originalPhoto = $this->photoStorage->getUrl($speaker, PhotoStorage::DIR_ORIGINAL);
            if (null === $photo && $event->getId() < self::ID_FORUM_PHOTO_STORAGE) {
                $photo = $this->photoStorage->getLegacyUrl($event, $speaker);
            }
        }
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $speaker->genre = $data->genre;
            $speaker->firstname = $data->firstname;
            $speaker->lastname = $data->lastname;
            $speaker->biography = $data->biography;
            $speaker->twitter = $data->twitter;
            $speaker->bluesky = $data->bluesky;
            $speaker->mastodon = $data->mastodon;
            $speaker->linkedin = $data->linkedin;
            $speaker->email = $data->email;
            $speaker->githubUserId = $data->githubUser instanceof GithubUser ? $data->githubUser->getId() : (is_int($data->githubUser) ? $data->githubUser : null);
            $speaker->company = $data->company;
            $speaker->locality = $data->locality;
            $speaker->phoneNumber = $data->phoneNumber;
            $speaker->referentPerson = $data->referentPerson;
            $speaker->referentPersonEmail = $data->referentPersonEmail;
            $this->speakerRepository->save($speaker);
            if ($data->photoFile) {
                if ($event->getId() < self::ID_FORUM_PHOTO_STORAGE) {
                    $fileName = $this->photoStorage->storeLegacy($data->photoFile, $event, $speaker);
                } else {
                    $fileName = $this->photoStorage->store($data->photoFile, $speaker);
                }
                $speaker->photo = $fileName;
            }
            $this->speakerRepository->save($speaker);
            $this->audit->log('Modification du conférencier de ' . $speaker->firstname . ' ' . $speaker->lastname . ' (' . $speaker->id . ')');

            $this->addFlash('notice', 'Le conférencier a été modifié');

            return $this->redirectToRoute('admin_speaker_list', [
                'eventId' => $event->getId(),
            ]);
        }

        $personne = $speaker->personneId !== null ? $this->personneConferencierRepository->find($speaker->personneId) : null;
        // Recherche d'une personne à laquelle rattacher la ligne (formulaire GET du bloc « Personne »), utile
        // seulement tant que la ligne n'est rattachée à personne.
        $personneSearch = $request->query->getString('personneQuery');
        $personneCandidates = $personne === null && $personneSearch !== '' ? array_values(array_filter(
            $this->personneConferencierRepository->searchAcrossEvents($personneSearch),
            static fn(SpeakerSearchResult $result): bool => $result->personneId !== null,
        )) : [];

        return $this->render('admin/speaker/edit.html.twig', [
            'speakerId' => $speaker->id,
            'eventId' => $event->getId(),
            'gravatar' => Utils::get_gravatar((string) $speaker->email, 90),
            'eventTitle' => $event->getTitle(),
            'form' => $form->createView(),
            'talks' => $talks,
            'photo' => $photo,
            'originalPhoto' => $originalPhoto,
            'personne' => $personne,
            'personneSearch' => $personneSearch,
            'personneCandidates' => $personneCandidates,
        ]);
    }
}
