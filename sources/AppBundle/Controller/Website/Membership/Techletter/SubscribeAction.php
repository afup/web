<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Membership\Techletter;

use AppBundle\Association\Model\Repository\TechletterSubscriptionsRepository;
use AppBundle\Association\Model\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

final class SubscribeAction extends AbstractController
{
    public function __construct(
        private readonly TechletterSubscriptionsRepository $techletterSubscriptionsRepository,
    ) {}

    public function __invoke(Request $request): RedirectResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createNotFoundException();
        }

        if (!$this->isCsrfTokenValid('techletter_subscription', $request->request->get('_csrf_token'))
            || $user->getLastSubscription() < new \DateTime()) {
            throw $this->createAccessDeniedException('You cannot subscribe to the techletter');
        }

        $this->addFlash('success', "Vous êtes maintenant abonné à la veille de l'AFUP");

        $this->techletterSubscriptionsRepository->subscribe($user);

        return $this->redirectToRoute('member_techletter');
    }
}
