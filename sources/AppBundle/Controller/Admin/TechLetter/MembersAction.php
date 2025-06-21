<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\TechLetter;

use AppBundle\Association\Model\Repository\TechletterSubscriptionsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class MembersAction extends AbstractController
{
    public function __construct(
        private readonly TechletterSubscriptionsRepository $techletterSubscriptionsRepository,
    ) {}

    public function __invoke(): Response
    {
        $subscribers = $this->techletterSubscriptionsRepository->getAllSubscriptionsWithUser();
        return $this->render('admin/techletter/members.html.twig', [
            'subscribers' => $subscribers,
            'title' => 'Liste des abonnés à la techletter',
        ]);
    }
}
