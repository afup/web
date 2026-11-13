<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\Lead;

use AppBundle\Email\Mailer\Mailer;
use AppBundle\Email\Mailer\MailUserFactory;
use AppBundle\Event\Entity\Repository\SponsorshipSettingsRepository;
use AppBundle\Event\Enum\BecomeSponsorMode;
use AppBundle\Event\Form\LeadType;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Lead;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Sponsorship\SponsorshipLeadMail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Demande de dossier de sponsoring de l'association, indépendante de tout évènement.
 * En mode « évènement », redirige vers la page de sponsoring du dernier évènement.
 */
final class BecomeSponsorGenericAction extends AbstractController
{
    public function __construct(
        private readonly Mailer $mailer,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly SponsorshipLeadMail $sponsorshipLeadMail,
        private readonly SponsorshipSettingsRepository $sponsorshipSettingsRepository,
        private readonly EventRepository $eventRepository,
    ) {}

    public function __invoke(Request $request): Response
    {
        $settings = $this->sponsorshipSettingsRepository->get();

        if ($settings->mode === BecomeSponsorMode::Event) {
            // Entre la fin d'un évènement et la création du suivant, il n'y a pas d'évènement courant
            $event = $this->eventRepository->getCurrentEvent() ?? $this->eventRepository->getLastEvent();

            // Sans aucun évènement, on se rabat sur la page générique
            if ($event instanceof Event) {
                return $this->redirectToRoute('sponsor_leads', ['eventSlug' => $event->getPath()]);
            }
        }

        // Sans dossier à envoyer, inutile de collecter la demande
        if (!$this->sponsorshipLeadMail->hasSponsorshipFile()) {
            throw $this->createNotFoundException('Sponsorship file not found');
        }

        $lead = new Lead();
        $lead->setLanguage($request->getLocale());
        $leadForm = $this->createForm(LeadType::class, $lead);

        $leadForm->handleRequest($request);

        if ($leadForm->isSubmitted() && $leadForm->isValid()) {
            $sponsorshipLeadMail = $this->sponsorshipLeadMail;
            $this->eventDispatcher->addListener(KernelEvents::TERMINATE, function () use ($lead, $sponsorshipLeadMail): void {
                $sponsorshipLeadMail->sendSponsorshipFile($lead);
            });

            $this->sendMailToTeamSponsor($lead);

            $this->addFlash('lead_email', $lead->getEmail());

            return $this->redirectToRoute('become_sponsor_generic_thanks');
        }

        return $this->render('event/sponsorship_file/form.html.twig', [
            'leadForm' => $leadForm->createView(),
            'become_sponsor_text' => $settings->getText($request->getLocale()),
        ]);
    }

    private function sendMailToTeamSponsor(Lead $lead): void
    {
        $subject = 'Nouvelle demande de dossier de sponsoring';

        $content
            = sprintf(
                "Une nouvelle demande de dosssier de sponsoring vient d'être effectuée sur le site. Voici les informations saisies :

                - Société: %s
                - Intitulé du poste: %s
                - Nom: %s
                - Prénom: %s
                - Email: %s
                - Téléphone: %s
                - Site web: %s
                - Langue: %s",
                $lead->getCompany(),
                $lead->getPoste(),
                $lead->getFirstname(),
                $lead->getLastname(),
                $lead->getEmail(),
                $lead->getPhone(),
                $lead->getWebsite(),
                $lead->getLanguage(),
            );

        $this->mailer->sendSimpleMessage($subject, $content, MailUserFactory::sponsors());
    }
}
