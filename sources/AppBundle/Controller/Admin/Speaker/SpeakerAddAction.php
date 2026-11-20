<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Speaker;

use AppBundle\AuditLog\Audit;
use AppBundle\CFP\PhotoStorage;
use AppBundle\Event\Form\SpeakerFormData;
use AppBundle\Event\Form\SpeakerFormDataFactory;
use AppBundle\Event\Form\SpeakerType;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Speaker;
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
        $source = $request->query->has('from') ? $this->speakerRepository->get($request->query->getInt('from')) : null;
        $sourcePhoto = null;
        if ($source instanceof Speaker) {
            $personneId = $this->personneConferencierLinker->findPersonneId($source);
            $existing = $personneId !== null ? $this->personneConferencierLinker->findRowInEvent($personneId, $event) : null;
            if ($existing instanceof Speaker) {
                $this->addFlash('notice', 'Ce conférencier existe déjà pour cet événement');

                return $this->redirectToRoute('admin_speaker_edit', ['id' => $existing->getId()]);
            }
            $data = $this->speakerFormDataFactory->fromSpeaker($source);
            if ($source->getPhoto() !== null) {
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
            $speaker->setEventId($event->getId());
            $speaker->setGenre($data->genre);
            $speaker->setFirstname($data->firstname);
            $speaker->setLastname($data->lastname);
            $speaker->setBiography($data->biography);
            $speaker->setTwitter($data->twitter);
            $speaker->setMastodon($data->mastodon);
            $speaker->setLinkedin($data->linkedin);
            $speaker->setBluesky($data->bluesky);
            $speaker->setEmail($data->email);
            $speaker->setUser($data->githubUser !== null ? $data->githubUser->getId() : null);
            $speaker->setCompany($data->company);
            $speaker->setLocality($data->locality);
            $speaker->setPhoneNumber($data->phoneNumber);
            $speaker->setReferentPerson($data->referentPerson);
            $speaker->setReferentPersonEmail($data->referentPersonEmail);
            if ($source instanceof Speaker) {
                // La source rejoint la même personne que la nouvelle ligne.
                if ($source->getPersonneId() === null) {
                    $this->personneConferencierLinker->ensurePersonne($source);
                    $this->speakerRepository->save($source);
                }
                $speaker->setPersonneId($source->getPersonneId());
            } else {
                $this->personneConferencierLinker->ensurePersonne($speaker);
            }
            $this->speakerRepository->save($speaker);
            if (null !== $data->photoFile) {
                $speaker->setPhoto($this->photoStorage->store($data->photoFile, $speaker));
                $this->speakerRepository->save($speaker);
            } elseif ($source instanceof Speaker && null !== $sourcePhoto) {
                $speaker->setPhoto($this->photoStorage->copyFrom($source, $speaker));
                $this->speakerRepository->save($speaker);
            }
            $this->audit->log('Ajout du conférencier de ' . $speaker->getFirstname() . ' ' . $speaker->getLastname()
                . ($source instanceof Speaker ? ' (repris de ' . $source->getId() . ')' : ''));

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
