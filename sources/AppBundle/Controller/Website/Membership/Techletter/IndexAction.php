<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Membership\Techletter;

use AppBundle\Association\Model\Repository\TechletterSubscriptionsRepository;
use AppBundle\Association\Model\User;
use AppBundle\TechLetter\Model\Repository\SendingRepository;
use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class IndexAction extends AbstractController
{
    public function __construct(
        private readonly ViewRenderer $view,
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
        private readonly TechletterSubscriptionsRepository $techletterSubscriptionsRepository,
        private readonly SendingRepository $sendingRepository,
    ) {}

    public function __invoke(): Response
    {
        if (!$this->getUser() instanceof User) {
            throw $this->createNotFoundException();
        }

        return $this->view->render('site/member/techletter.html.twig', [
            'subscribed' => $this->techletterSubscriptionsRepository->hasUserSubscribed($this->getUser()),
            'feeUpToDate' => $this->getUser()->getLastSubscription() > new \DateTime(),
            'token' => $this->csrfTokenManager->getToken('techletter_subscription'),
            'techletter_history' => $this->sendingRepository->getAllPastSent(),
        ]);
    }
}
