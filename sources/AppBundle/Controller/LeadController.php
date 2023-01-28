<?php

namespace AppBundle\Controller;

use AppBundle\Email\Mailer\Mailer;
use AppBundle\Email\Mailer\MailUserFactory;
use AppBundle\Event\Form\LeadType;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Lead;
use AppBundle\Event\Model\Repository\EventRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;

class LeadController extends EventBaseController
{

    /** @var Mailer */
    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function becomeSponsorAction($eventSlug, Request $request)
    {
        $event = $this->checkEventSlug($eventSlug);

        $lead = new Lead();
        $lead
            ->setEvent($event)
            ->setLanguage($request->getLocale())
        ;
        $leadForm = $this->createForm(LeadType::class, $lead);

        $leadForm->handleRequest($request);

        if ($leadForm->isSubmitted() && $leadForm->isValid()) {
            $repository = $this->get(\AppBundle\Event\Model\Repository\LeadRepository::class);
            $repository->save($lead);

            $sponsorshipLeadMail = $this->get(\AppBundle\Event\Sponsorship\SponsorshipLeadMail::class);
            $this->get('event_dispatcher')->addListener(KernelEvents::TERMINATE, function () use ($lead, $sponsorshipLeadMail) {
                $sponsorshipLeadMail->sendSponsorshipFile($lead);
            });

            $this->sendMailToTeamSponsor($event, $lead);

            return $this->redirectToRoute('sponsor_leads_post', ['eventSlug' => $eventSlug]);
        }

        return $this->render('event/sponsorship_file/form.html.twig', [
            'event' => $event,
            'leadForm' => $leadForm->createView()
        ]);
    }

    public function postLeadAction($eventSlug)
    {
        $event = $this->checkEventSlug($eventSlug);
        return $this->render(':event/sponsorship_file:thanks.html.twig', ['event' => $event]);
    }

    /**
     * Redirige vers la page de sponsoring du dernier évènement.
     *
     * @return RedirectResponse
     */
    public function becomeSponsorLatestAction()
    {
        $event = $this->get('ting')->get(EventRepository::class)->getCurrentEvent();

        return new RedirectResponse($this->generateUrl('sponsor_leads', ['eventSlug' => $event->getPath()]));
    }

    /**
     * @param Event $event
     * @param Lead $lead
     */
    private function sendMailToTeamSponsor(Event $event, Lead $lead)
    {
        $subject = sprintf('%s - Nouvelle demande de dossier de sponsoring', $event->getTitle());

        $content = 
            sprintf( 
                "Une nouvelle demande de dosssier de sponsoring vient d'être effectuée sur le site. Voici les informations saisies :
                
                - Société: %s
                - Nom: %s
                - Prénom: %s
                - Email: %s
                - Téléphone: %s
                - Website: %s
                - Langue: %s", 
                $lead->getCompany(),
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
