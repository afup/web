<?php

namespace AppBundle\Controller;

use AppBundle\Event\Form\LeadType;
use AppBundle\Event\Model\Lead;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;

class LeadController extends EventBaseController
{
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
