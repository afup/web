<?php

namespace AppBundle\Controller;

use Afup\Site\Forum\Facturation;
use AppBundle\Event\Form\LeadType;
use AppBundle\Event\Form\SponsorTicketType;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Invoice;
use AppBundle\Event\Model\Lead;
use AppBundle\Event\Model\Repository\LeadRepository;
use AppBundle\Event\Model\Repository\SponsorTicketRepository;
use AppBundle\Event\Model\Repository\TicketRepository;
use AppBundle\Event\Model\SponsorTicket;
use AppBundle\Event\Model\Ticket;
use AppBundle\Payment\PayboxResponse;
use AppBundle\Payment\PayboxResponseFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;
use Trello\Client;
use Trello\Manager;

class LeadController extends EventBaseController
{
    public function becomeSponsorAction($eventSlug, Request $request)
    {
        $event = $this->checkEventSlug($eventSlug);

        $lead = new Lead();
        $lead
            ->setEvent($event)
        ;
        $leadForm = $this->createForm(LeadType::class, $lead);

        $leadForm->handleRequest($request);

        if ($leadForm->isSubmitted() && $leadForm->isValid()) {
            $repository = $this->get('app.lead_repository');
            $repository->save($lead);

            $sponsorshipLeadMail = $this->get('app.sponsorship_lead_mailer');
            $this->get('event_dispatcher')->addListener(KernelEvents::TERMINATE, function () use ($lead, $sponsorshipLeadMail) {
                $sponsorshipLeadMail->sendSponsorshipFile($lead);
            });

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
}
