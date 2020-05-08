<?php

namespace AppBundle\Controller\Admin\Membership;

use AppBundle\Association\Model\Repository\SubscriptionReminderLogRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ReminderLogAction
{
    /** @var SubscriptionReminderLogRepository */
    private $subscriptionReminderLogRepository;
    /** @var Environment */
    private $twig;

    public function __construct(
        SubscriptionReminderLogRepository $subscriptionReminderLogRepository,
        Environment $twig
    ) {
        $this->subscriptionReminderLogRepository = $subscriptionReminderLogRepository;
        $this->twig = $twig;
    }

    public function __invoke(Request $request)
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
