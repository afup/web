<?php


namespace AppBundle\Controller;

use AppBundle\Association\Form\CompanyMemberType;
use AppBundle\Mailchimp\SubscriberType;
use Symfony\Component\HttpFoundation\Request;

class NewsletterController extends SiteBaseController
{
    public function subscribeFormAction()
    {
        return $this->render(':site/newsletter:subscribe.html.twig', ['form' => $this->getSubscriberType()->createView()]);
    }

    public function subscribeAction(Request $request)
    {
        $subscribeForm = $this->getSubscriberType();
        $subscribeForm->handleRequest($request);

        if ($subscribeForm->isSubmitted() && $subscribeForm->isValid()) {
            return $this->render('');
        }

        return $this->render(':site/company_membership:adhesion_entreprise.html.twig', ['form' => $subscribeForm->createView()]);
    }

    private function getSubscriberType()
    {
        return $this
            ->createForm(SubscriberType::class, null, [
                'action' => $this->generateUrl('newsletter_subscribe'),
                'method' => Request::METHOD_GET
            ])
        ;
    }
}
