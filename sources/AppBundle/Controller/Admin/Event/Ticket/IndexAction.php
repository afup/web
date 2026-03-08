<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event\Ticket;

use AppBundle\Event\AdminEventSelection;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\EventStats;
use AppBundle\Event\Model\Repository\EventStatsRepository;
use AppBundle\Event\Model\Repository\TicketRepository;
use AppBundle\Event\Ticket\TicketOffers;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IndexAction extends AbstractController
{
    public function __construct(
        private readonly EventStatsRepository $eventStatsRepository,
        private readonly TicketRepository $ticketRepository,
        private readonly TicketOffers $ticketOffer,
        private readonly FormFactoryInterface $formFactory,
    ) {}

    public function __invoke(Request $request, AdminEventSelection $eventSelection): Response
    {
        //TODO : à supprimer quand les actions via le formulaire auront été migrées
        if (isset($_SESSION['flash']['message'])) {
            $this->addFlash('notice', $_SESSION['flash']['message']);
        }
        if (isset($_SESSION['flash']['erreur'])) {
            $this->addFlash('error', $_SESSION['flash']['erreur']);
        }
        unset($_SESSION['flash']);


        $event = $eventSelection->event;
        $data = [
            'id' => $event->getId(),
        ];
        $filterForm = $this->filterForm($data);
        $filterForm->handleRequest($request);

        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $data = $filterForm->getData();
            $data = array_filter($data);
        }
        $data['sort_key'] ??= 'date';
        $data['sort_direction'] ??= 'desc';

        $statistics = $this->eventStatsRepository->getStats($event->getId());
        $tickets = $this->ticketRepository->getByEventWithAll(
            event: $event,
            search: $data['filter'] ?? null,
            sortKey: $data['sort_key'],
            sortDirection: $data['sort_direction'],
        );
        $computed = $this->computeStatistics($statistics, $event);

        return $this->render('admin/event/ticket/index.html.twig', [
            'event' => $event,
            'event_select_form' => $eventSelection->selectForm(),
            'statistics' => $statistics,
            'tickets' => $tickets,
            'computed' => $computed,
            'filter_form' => $filterForm,
            'filter' => $data,
        ]);
    }

    private function computeStatistics(EventStats $statistics, Event $event): array
    {
        $computed = [];
        $offers = $this->ticketOffer->getAllOffersForEvent($event);

        foreach ($offers as $ticketType => $ticketOffer) {

            $registered = $statistics->ticketType->registered[$ticketType] ?? 0;
            $confirmed = $statistics->ticketType->confirmed[$ticketType] ?? 0;
            $paying = $statistics->ticketType->paying[$ticketType] ?? 0;
            $amount = $paying * $ticketOffer->price;

            if ($registered) {
                $computed[$ticketType] = [
                    'label' => $ticketOffer->name,
                    'registered' => $registered,
                    'confirmed' => $confirmed,
                    'paying' => $paying,
                    'amount' => $ticketOffer->price,
                    'payingAmount' => $amount,
                    'availableTickets' => $ticketOffer->availableTickets,
                ];
            }
        }

        return $computed;
    }

    private function filterForm(array $data): FormInterface
    {
        return $this->formFactory->createNamedBuilder('', FormType::class, $data, [
            'csrf_protection' => false,
        ])
            ->setMethod('GET')
            ->add('filter', TextType::class, ['required' => false])
            ->add('id', HiddenType::class, ['required' => false])
            ->add('sort_key', HiddenType::class, ['required' => false])
            ->add('sort_direction', HiddenType::class, ['required' => false])
            ->add('submit', SubmitType::class)
            ->getForm();
    }
}
