<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\TechLetter;

use AppBundle\Veille\Entity\Repository\NewsletterInscriptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class MembersAction extends AbstractController
{
    public function __construct(private readonly NewsletterInscriptionRepository $newsletterInscriptionRepository) {}

    public function __invoke(): Response
    {
        $subscribers = $this->newsletterInscriptionRepository->getAllSubscriptionsWithUser();
        return $this->render('admin/techletter/members.html.twig', [
            'subscribers' => $subscribers,
            'title' => 'Liste des abonnés à la techletter',
        ]);
    }
}
