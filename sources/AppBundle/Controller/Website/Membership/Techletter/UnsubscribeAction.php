<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Membership\Techletter;

use AppBundle\Veille\Entity\Repository\NewsletterDesinscriptionRepository;
use AppBundle\Security\Authentication;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class UnsubscribeAction extends AbstractController
{
    public function __construct(
        private readonly NewsletterDesinscriptionRepository $newsletterDesinscriptionRepository,
        private readonly Authentication $authentication,
    ) {}

    public function __invoke(): RedirectResponse
    {
        $techletterUnsubscription = $this->newsletterDesinscriptionRepository->createFromUser($this->authentication->getAfupUser());
        $this->newsletterDesinscriptionRepository->save($techletterUnsubscription);
        $this->addFlash('success', "Vous êtes maintenant désabonné à la veille de l'AFUP");
        return $this->redirectToRoute('member_techletter');
    }
}
