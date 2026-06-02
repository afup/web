<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Membership\Techletter;

use AppBundle\Veille\Entity\Repository\NewsletterInscriptionRepository;
use AppBundle\Security\Authentication;
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
        private readonly NewsletterInscriptionRepository $newsletterInscriptionRepository,
        private readonly SendingRepository $sendingRepository,
        private readonly Authentication $authentication,
    ) {}

    public function __invoke(): Response
    {
        return $this->view->render('site/member/techletter.html.twig', [
            'subscribed' => $this->newsletterInscriptionRepository->hasUserSubscribed($this->authentication->getAfupUser()),
            'feeUpToDate' => $this->authentication->getAfupUser()->getLastSubscription() > new \DateTime(),
            'token' => $this->csrfTokenManager->getToken('techletter_subscription'),
            'techletter_history' => $this->sendingRepository->getAllPastSent(),
        ]);
    }
}
