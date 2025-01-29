<?php


namespace AppBundle\Controller\Website;

use AppBundle\Mailchimp\Mailchimp;
use AppBundle\Mailchimp\SubscriberType;
use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class NewsletterController extends Controller
{
    private ViewRenderer $view;

    public function __construct(ViewRenderer $view)
    {
        $this->view = $view;
    }

    public function subscribeFormAction()
    {
        return $this->render('site/newsletter/subscribe.html.twig', [
            'form' => $this->getSubscriberType()->createView()
        ]);
    }

    public function subscribeAction(Request $request)
    {
        $subscribeForm = $this->getSubscriberType();
        $subscribeForm->handleRequest($request);

        if ($subscribeForm->isSubmitted() && $subscribeForm->isValid()) {
            try {
                $this->get(Mailchimp::class)->subscribeAddress(
                    $this->getParameter('mailchimp_subscribers_list'),
                    $subscribeForm->getData()['email']
                );
                $success = true;
            } catch (\Exception $e) {
                $success = false;
            }
            return $this->view->render('site/newsletter/postsubscribe.html.twig', [
                'success' => $success
            ]);
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
