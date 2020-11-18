<?php

namespace App\RendezVous;

use Afup\Site\Utils\Configuration;
use Afup\Site\Utils\Mailing;
use AppBundle\Association\Form\HTML_QuickForm;
use AppBundle\Email\Mailer\MailUser;
use AppBundle\Email\Mailer\Message;
use Assert\Assertion;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RendezVousService
{
    /** @var UrlGeneratorInterface */
    private $urlGenerator;
    /** @var MailUser */
    private $sender;
    /** @var RendezVousAttendeeRepository */
    private $rendezVousAttendeeRepository;
    /** @var RendezVousRepository */
    private $rendezVousRepository;
    /** @var RendezVousSlideRepository */
    private $rendezVousSlideRepository;

    public function __construct(
        RendezVousRepository $rendezVousRepository,
        RendezVousAttendeeRepository $rendezVousAttendeeRepository,
        RendezVousSlideRepository $rendezVousSlideRepository,
        UrlGeneratorInterface $urlGenerator,
        Configuration $configuration
    ) {
        $this->rendezVousRepository = $rendezVousRepository;
        $this->rendezVousAttendeeRepository = $rendezVousAttendeeRepository;
        $this->urlGenerator = $urlGenerator;
        $this->sender = new MailUser($configuration->obtenir('mails|email_expediteur'), $configuration->obtenir('mails|nom_expediteur'));
        $this->rendezVousSlideRepository = $rendezVousSlideRepository;
    }

    public function fillWithWaitingList(RendezVous $rendezVous)
    {
        $this->rendezVousAttendeeRepository->refuseDeclinedInvitations($rendezVous);
        $this->rendezVousAttendeeRepository->fillFreeSpotsWithPending($rendezVous);
    }

    /**
     * @param string $subject
     * @param string $body
     *
     * @return int
     */
    public function sendRSVPs(RendezVous $rendezVous, $subject, $body)
    {
        $attendees = $this->rendezVousAttendeeRepository->findComingUnconfirmed($rendezVous);
        $success = 0;
        foreach ($attendees as $attendee) {
            $hash = md5(utf8_decode($attendee->getId().$attendee->getRendezVousId().$attendee->getLastname().$attendee->getFirstname().$attendee->getEmail()));
            $url = $this->urlGenerator->generate('legacy_rendezvous_confirmation', ['hash' => $hash], UrlGeneratorInterface::ABSOLUTE_URL);
            Mailing::envoyerMail(new Message(
                $subject,
                $this->sender,
                new MailUser($attendee->getEmail(), $attendee->getLastname())
            ), $body.PHP_EOL.$url);
            ++$success;
        }

        return $success;
    }

    public function isFull(RendezVous $rendezVous)
    {
        return !$this->getPossiblePresence($rendezVous);
    }

    public function registerAttendee(HTML_QuickForm $formulaire)
    {
        $id_rendezvous = (int) $formulaire->exportValue('id_rendezvous');
        if ($id_rendezvous <= 0) {
            return;
        }
        $rendezVous = $this->rendezVousRepository->find($id_rendezvous);
        Assertion::notNull($rendezVous);
        $id = $formulaire->exportValue('id');
        if ($id > 0) {
            $attendee = $this->rendezVousAttendeeRepository->get($id);
            Assertion::notNull($attendee);
            $attendee->setPresence($formulaire->exportValue('presence'));
        } else {
            $attendee = new RendezVousAttendee();
            $attendee->setRendezVousId($rendezVous->getId());
            $attendee->setCreation(time());
            $attendee->setPresence($this->getPossiblePresence($rendezVous));
        }
        $attendee->setLastname($formulaire->exportValue('nom'));
        $attendee->setFirstname($formulaire->exportValue('prenom'));
        $attendee->setCompany($formulaire->exportValue('entreprise'));
        $attendee->setEmail($formulaire->exportValue('email'));
        $attendee->setPhone($formulaire->exportValue('telephone'));
        $attendee->setConfirmed($formulaire->exportValue('confirme'));
        $this->rendezVousAttendeeRepository->save($attendee);
    }

    public function accepteSurListeAttenteUniquement(RendezVous $rendezVous)
    {
        $inscrits = $this->rendezVousAttendeeRepository->countComing($rendezVous) + $this->rendezVousAttendeeRepository->countPending($rendezVous);

        return $inscrits > floor($rendezVous->getCapacity() * RendezVous::COEF_COMING)
            && $inscrits <= floor($rendezVous->getCapacity() * RendezVous::COEF_PENDING);
    }

    public function getBarCampExportList(RendezVous $rendezVous)
    {
        $list = [];
        $i = 0;
        foreach ($this->rendezVousAttendeeRepository->findComingAndPendingByRendezVous($rendezVous) as $attendee) {
            $list[] = [
                'id' => ++$i,
                'firstname' => $attendee->getFirstname(),
                'lastname' => $attendee->getLastname(),
                'company' => $attendee->getCompany(),
                'email' => str_replace(['@', '.'], [' (at) ', ' (dot) '], $attendee->getEmail()),
            ];
        }

        return $list;
    }

    public function getPossiblePresence(RendezVous $rendezVous)
    {
        $registered = $this->rendezVousAttendeeRepository->countComing($rendezVous) + $this->rendezVousAttendeeRepository->countPending($rendezVous);
        if ($registered <= floor($rendezVous->getCapacity() * RendezVous::COEF_COMING)) {
            return RendezVousAttendee::COMING;
        }
        if ($registered <= floor($rendezVous->getCapacity() * RendezVous::COEF_PENDING)) {
            return RendezVousAttendee::PENDING;
        }

        return RendezVousAttendee::REFUSED;
    }
}
