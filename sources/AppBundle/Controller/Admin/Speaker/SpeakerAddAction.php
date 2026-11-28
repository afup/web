<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Speaker;

use AppBundle\AuditLog\Audit;
use AppBundle\CFP\PhotoStorage;
use AppBundle\Event\Entity\GithubUser;
use AppBundle\Event\Form\SpeakerFormData;
use AppBundle\Event\Form\SpeakerFormDataFactory;
use AppBundle\Event\Form\SpeakerType;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Entity\Repository\SpeakerRepository;
use AppBundle\Event\Entity\Speaker;
use AppBundle\Event\Speaker\PersonneConferencierLinker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_EVENT_WRITER')]
class SpeakerAddAction extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly SpeakerRepository $speakerRepository,
        private readonly PhotoStorage $photoStorage,
        private readonly SpeakerFormDataFactory $speakerFormDataFactory,
        private readonly PersonneConferencierLinker $personneConferencierLinker,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request): Response
    {
        $event = $this->eventRepository->get($request->query->get('eventId'));
        Assert::notNull($event);

        // Reprise d'un conférencier d'une édition passée (?from=<speakerId>)
        $source = $request->query->has('from') ? $this->speakerRepository->find($request->query->getInt('from')) : null;
        $sourcePhoto = null;
        if ($source instanceof Speaker) {
            $personneId = $this->personneConferencierLinker->findPersonneId($source);
            $existing = $personneId !== null ? $this->personneConferencierLinker->findRowInEvent($personneId, $event) : null;
            if ($existing instanceof Speaker) {
                $this->addFlash('notice', 'Ce conférencier existe déjà pour cet événement');

                return $this->redirectToRoute('admin_speaker_edit', ['id' => $existing->id]);
            }
            $data = $this->speakerFormDataFactory->fromSpeaker($source);
            if ($source->photo !== null) {
                $sourcePhoto = $this->photoStorage->getUrl($source, PhotoStorage::DIR_ORIGINAL);
            }
        } else {
            $data = new SpeakerFormData();
        }

        $form = $this->createForm(SpeakerType::class, $data, [
            SpeakerType::OPT_USER_GITHUB => true,
            SpeakerType::OPT_PHOTO_REQUIRED => null === $sourcePhoto,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $speaker = new Speaker();
            $speaker->eventId = (int) $event->getId();
            $speaker->genre = $data->genre;
            $speaker->firstname = $data->firstname;
            $speaker->lastname = $data->lastname;
            $speaker->biography = $data->biography;
            $speaker->twitter = $data->twitter;
            $speaker->mastodon = $data->mastodon;
            $speaker->linkedin = $data->linkedin;
            $speaker->bluesky = $data->bluesky;
            $speaker->email = $data->email;
            $speaker->githubUserId = $data->githubUser instanceof GithubUser ? $data->githubUser->getId() : (is_int($data->githubUser) ? $data->githubUser : null);
            $speaker->company = $data->company;
            $speaker->locality = $data->locality;
            $speaker->phoneNumber = $data->phoneNumber;
            $speaker->referentPerson = $data->referentPerson;
            $speaker->referentPersonEmail = $data->referentPersonEmail;
            if ($source instanceof Speaker) {
                // La source rejoint la même personne que la nouvelle ligne.
                if ($source->personneId === null) {
                    $this->personneConferencierLinker->ensurePersonne($source);
                    $this->speakerRepository->save($source);
                }
                $speaker->personneId = $source->personneId;
            } else {
                $this->personneConferencierLinker->ensurePersonne($speaker);
            }
            // Une personne n'a qu'une fiche par édition : son compte GitHub peut désigner une personne déjà inscrite.
            $existing = $this->personneConferencierLinker->findRowInEvent((int) $speaker->personneId, $event);
            if ($existing instanceof Speaker) {
                $this->addFlash('notice', 'Ce conférencier existe déjà pour cet événement');

                return $this->redirectToRoute('admin_speaker_edit', ['id' => $existing->id]);
            }
            $this->speakerRepository->save($speaker);
            if (null !== $data->photoFile) {
                $speaker->photo = $this->photoStorage->store($data->photoFile, $speaker);
                $this->speakerRepository->save($speaker);
            } elseif ($source instanceof Speaker && null !== $sourcePhoto) {
                $speaker->photo = $this->photoStorage->copyFrom($source, $speaker);
                $this->speakerRepository->save($speaker);
            }
            $this->audit->log('Ajout du conférencier de ' . $speaker->firstname . ' ' . $speaker->lastname
                . ($source instanceof Speaker ? ' (repris de ' . $source->id . ')' : ''));

            $this->addFlash('notice', 'Le conférencier a été ajouté');

            return $this->redirectToRoute('admin_speaker_list', [
                'eventId' => $event->getId(),
            ]);
        }

        return $this->render('admin/speaker/add.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
            'source' => $source,
            'sourcePhoto' => $sourcePhoto,
        ]);
    }
}
