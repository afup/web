<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website;

use AppBundle\Mailchimp\Mailchimp;
use AppBundle\Mailchimp\SubscriberType;
use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NewsletterController extends AbstractController
{
    public function __construct(
        private readonly ViewRenderer $view,
        #[Autowire('@app.mailchimp_api')]
        private readonly Mailchimp $mailchimp,
        #[Autowire('%env(MAILCHIMP_SUBSCRIBERS_LIST)%')]
        private readonly string $mailchimpSubscribersList,
    ) {}

    public function subscribeForm(): Response
    {
        return $this->render('site/newsletter/subscribe.html.twig', [
            'form' => $this->getSubscriberType()->createView(),
        ]);
    }

    public function subscribe(Request $request)
    {
        $subscribeForm = $this->getSubscriberType();
        $subscribeForm->handleRequest($request);

        if ($subscribeForm->isSubmitted() && $subscribeForm->isValid()) {
            try {
                $this->mailchimp->subscribeAddress(
                    $this->mailchimpSubscribersList,
                    $subscribeForm->getData()['email'],
                );
                $success = true;
            } catch (\Exception) {
                $success = false;
            }
            return $this->view->render('site/newsletter/postsubscribe.html.twig', [
                'success' => $success,
            ]);
        }

        return $this->redirect('/');
    }

    private function getSubscriberType(): FormInterface
    {
        return $this
            ->createForm(SubscriberType::class, null, [
                'action' => $this->generateUrl('newsletter_subscribe'),
                'method' => Request::METHOD_POST,
            ])
        ;
    }
}
