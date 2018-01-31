<?php

namespace AppBundle\Controller;

use AppBundle\Association\Model\Repository\TechletterSubscriptionsRepository;
use Symfony\Component\HttpFoundation\Request;

class TechletterController extends SiteBaseController
{
    public function indexAction()
    {
        return $this->render('site/techletter/index.html.twig', [
            'subscribed' => $this->get('ting')->get(TechletterSubscriptionsRepository::class)->hasUserSubscribed($this->getUser()),
            'loggedIn' => ($this->getUser() !== null),
            'feeUpToDate' => ($this->getUser() !== null and $this->getUser()->getLastSubscription() > new \DateTime()),
            'token' => $this->get('security.csrf.token_manager')->getToken('techletter_subscription')
        ]);
    }

    public function subscribeAction(Request $request)
    {
        $user = $this->getUser();
        $token = $this->get('security.csrf.token_manager')->getToken('techletter_subscription');

        if (
            $user === null
            || $user->getLastSubscription() < new \DateTime()
            || $request->request->has('_csrf_token') === false
            || $request->request->get('_csrf_token') !== $token->getValue()
        ) {
            throw $this->createAccessDeniedException('You cannot subscribe to the techletter');
        }

        $this->get('ting')->get(TechletterSubscriptionsRepository::class)->subscribe($user);
        return $this->render('site/techletter/subscribe.html.twig');
    }
}
