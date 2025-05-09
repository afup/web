<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event;

use AppBundle\Email\Mailer\Mailer;
use AppBundle\Email\Mailer\MailUserFactory;
use AppBundle\Event\Form\LeadType;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Lead;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Sponsorship\SponsorshipLeadMail;
use CCMBenchmark\TingBundle\Repository\RepositoryFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;

class LeadController extends AbstractController
{
    public function __construct(
        private readonly Mailer $mailer,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly SponsorshipLeadMail $sponsorshipLeadMail,
        private readonly RepositoryFactory $repositoryFactory,
        private readonly EventActionHelper $eventActionHelper,
    ) {
    }

    public function becomeSponsor($eventSlug, Request $request)
    {
        $event = $this->eventActionHelper->getEvent($eventSlug);

        $lead = new Lead();
        $lead
            ->setEvent($event)
            ->setLanguage($request->getLocale())
        ;
        $leadForm = $this->createForm(LeadType::class, $lead);

        $leadForm->handleRequest($request);

        if ($leadForm->isSubmitted() && $leadForm->isValid()) {
            $sponsorshipLeadMail = $this->sponsorshipLeadMail;
            $this->eventDispatcher->addListener(KernelEvents::TERMINATE, function () use ($lead, $sponsorshipLeadMail): void {
                $sponsorshipLeadMail->sendSponsorshipFile($lead);
            });

            $this->sendMailToTeamSponsor($event, $lead);

            return $this->redirectToRoute('sponsor_leads_post', ['eventSlug' => $eventSlug]);
        }

        return $this->render('event/sponsorship_file/form.html.twig', [
            'event' => $event,
            'leadForm' => $leadForm->createView(),
        ]);
    }

    public function postLead($eventSlug): Response
    {
        $event = $this->eventActionHelper->getEvent($eventSlug);
        return $this->render('event/sponsorship_file/thanks.html.twig', ['event' => $event]);
    }

    /**
     * Redirige vers la page de sponsoring du dernier évènement.
     *
     * @return RedirectResponse
     */
    public function becomeSponsorLatest()
    {
        $event = $this->repositoryFactory->get(EventRepository::class)->getCurrentEvent();

        return new RedirectResponse($this->generateUrl('sponsor_leads', ['eventSlug' => $event->getPath()]));
    }

    private function sendMailToTeamSponsor(Event $event, Lead $lead): void
    {
        $subject = sprintf('%s - Nouvelle demande de dossier de sponsoring', $event->getTitle());

        $content =
            sprintf(
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
                $lead->getLanguage()
            );

        $this->mailer->sendSimpleMessage($subject, $content, MailUserFactory::sponsors());
    }
}
