<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Membership;

use AppBundle\Association\Model\Repository\SubscriptionReminderLogRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ReminderLogAction
{
    public function __construct(
        private readonly SubscriptionReminderLogRepository $subscriptionReminderLogRepository,
        private readonly Environment $twig,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $page = $request->attributes->getInt('page', 1);
        $limit = 50;

        return new Response($this->twig->render('admin/relances/liste.html.twig', [
            'logs' => $this->subscriptionReminderLogRepository->getPaginatedLogs($page, $limit),
            'limit' => $limit,
            'page' => $page,
            'title' => 'Relances',
        ]));
    }
}
