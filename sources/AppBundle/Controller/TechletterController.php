<?php

namespace AppBundle\Controller;

use AppBundle\Association\Model\Repository\TechletterSubscriptionsRepository;
use Symfony\Component\HttpFoundation\Request;

class TechletterController extends SiteBaseController
{
    public function subscribeAction(Request $request)
    {
        return $this->render('site/techletter/subscribe.html.twig', [
            'subscribed' => $this->get('ting')->get(TechletterSubscriptionsRepository::class)->hasUserSubscribed($this->getUser()),
            'loggedIn' => ($this->getUser() !== null),
            'feeUpToDate' => ($this->getUser() !== null and $this->getUser()->getLastSubscription() > new \DateTime())
        ]);
    }
}
