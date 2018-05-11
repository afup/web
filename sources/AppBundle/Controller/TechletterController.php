<?php

namespace AppBundle\Controller;

use AppBundle\Association\Model\Repository\TechletterSubscriptionsRepository;
use AppBundle\Association\Model\Repository\TechletterUnsubscriptionsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function webhookAction(Request $request)
    {
        if ($request->get('webhook_key') != $this->getParameter('mailchimp_techletter_webhook_key')) {
            return new Response('ko', 401);
        }

        if (Request::METHOD_GET == $request->getMethod()) {
            return new Response('ok');
        }

        if ($request->get('type') == 'unsubscribe') {
            $techletterUnsubscriptionRepository = $this->get('ting')->get(TechletterUnsubscriptionsRepository::class);
            $techletterUnsubscription = $techletterUnsubscriptionRepository->createFromWebhookData($request->get('data', []));
            $techletterUnsubscriptionRepository->save($techletterUnsubscription);
        }

        return new Response('ok');
    }
}
