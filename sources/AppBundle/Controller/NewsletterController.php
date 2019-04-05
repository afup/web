<?php


namespace AppBundle\Controller;

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
            try {
                $this->get(\AppBundle\Mailchimp\Mailchimp::class)->subscribeAddress(
                    $this->getParameter('mailchimp_subscribers_list'),
                    $subscribeForm->getData()['email']
                );
                $success = true;
            } catch (\Exception $e) {
                $success = false;
            }
            return $this->render(':site/newsletter:postsubscribe.html.twig', ['success' => $success]);
        }

        return $this->redirect('/');
    }

    private function getSubscriberType()
    {
        return $this
            ->createForm(SubscriberType::class, null, [
                'action' => $this->generateUrl('newsletter_subscribe'),
                'method' => Request::METHOD_POST
            ])
        ;
    }
}
