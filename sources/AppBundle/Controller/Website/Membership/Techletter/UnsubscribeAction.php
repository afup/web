<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Membership\Techletter;

use AppBundle\Association\Model\Repository\TechletterUnsubscriptionsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class UnsubscribeAction extends AbstractController
{
    public function __construct(
        private readonly TechletterUnsubscriptionsRepository $techletterUnsubscriptionsRepository,
    ) {}

    public function __invoke(): RedirectResponse
    {
        $techletterUnsubscription = $this->techletterUnsubscriptionsRepository->createFromUser($this->getUser());
        $this->techletterUnsubscriptionsRepository->save($techletterUnsubscription);
        $this->addFlash('success', "Vous êtes maintenant désabonné à la veille de l'AFUP");
        return $this->redirectToRoute('member_techletter');
    }
}
