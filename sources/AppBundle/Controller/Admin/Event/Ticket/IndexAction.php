<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event\Ticket;

use AppBundle\Event\AdminEventSelection;
use AppBundle\Event\Entity\Repository\BilleteriePriveeRepository;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\EventStats;
use AppBundle\Event\Model\EventStats\SpecialPriceStats;
use AppBundle\Event\Model\Repository\EventStatsRepository;
use AppBundle\Event\Model\Repository\TicketRepository;
use AppBundle\Event\Model\Repository\TicketSpecialPriceRepository;
use AppBundle\Event\Model\Ticket;
use AppBundle\Event\Model\TicketAggregate;
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
        private readonly BilleteriePriveeRepository $billeteriePriveeRepository,
        private readonly TicketSpecialPriceRepository $ticketSpecialPriceRepository,
        private readonly FormFactoryInterface $formFactory,
    ) {}

    public function __invoke(Request $request, AdminEventSelection $eventSelection): Response
    {
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
        $specialPriceLabels = $this->getSpecialPriceLabels($tickets);

        return $this->render('admin/event/ticket/index.html.twig', [
            'event' => $event,
            'event_select_form' => $eventSelection->selectForm(),
            'statistics' => $statistics,
            'tickets' => $tickets,
            'computed' => $computed,
            'special_price_labels' => $specialPriceLabels,
            'filter_form' => $filterForm,
            'filter' => $data,
        ]);
    }

    /**
     * Libellé explicite du tarif spécial lié à chaque inscription concernée
     * (billetterie privée prioritaire sur le token visiteur).
     *
     * @param array<TicketAggregate> $tickets
     * @return array<string, string> Libellé par token
     */
    private function getSpecialPriceLabels(array $tickets): array
    {
        $tokens = [];
        foreach ($tickets as $aggregate) {
            if (
                $aggregate->ticketType->getId() === Ticket::TYPE_SPECIAL_PRICE
                && $aggregate->ticket->getSpecialPriceToken() !== null
            ) {
                $tokens[] = $aggregate->ticket->getSpecialPriceToken();
            }
        }

        if ($tokens === []) {
            return [];
        }

        return $this->buildSpecialPriceLabels($tokens);
    }

    /**
     * @param list<string> $tokens
     * @return array<string, string> Libellé par token
     */
    private function buildSpecialPriceLabels(array $tokens): array
    {
        $labels = [];
        foreach ($this->billeteriePriveeRepository->findByTokens($tokens) as $billeteriePrivee) {
            $labels[$billeteriePrivee->token] = sprintf('Billeterie privée - %s', $billeteriePrivee->nom);
        }

        foreach ($this->ticketSpecialPriceRepository->findByTokens($tokens) as $ticketSpecialPrice) {
            if (isset($labels[$ticketSpecialPrice->getToken()])) {
                continue;
            }
            $labels[$ticketSpecialPrice->getToken()] = sprintf('Token visiteurs - %s', $ticketSpecialPrice->getDescription());
        }

        return $labels;
    }

    private function computeStatistics(EventStats $statistics, Event $event): array
    {
        $computed = $this->computeSpecialPriceStatistics($statistics, $event);
        $offers = $this->ticketOffer->getAllOffersForEvent($event);

        foreach ($offers as $ticketType => $ticketOffer) {

            if ((int) $ticketType === Ticket::TYPE_SPECIAL_PRICE) {
                // Les places à tarif spécial sont présentées en deux lignes
                // (tokens visiteurs / billetteries privées), voir computeSpecialPriceStatistics
                continue;
            }

            $registered = $statistics->ticketType->registered[$ticketType] ?? 0;
            $confirmed = $statistics->ticketType->confirmed[$ticketType] ?? 0;
            $paying = $statistics->ticketType->paying[$ticketType] ?? 0;
            $realAmount = $statistics->ticketType->realAmounts[$ticketType] ?? 0.0;
            $amount = $realAmount;

            if ($registered) {
                $computed[$ticketType] = [
                    'label' => $ticketOffer->name,
                    'registered' => $registered,
                    'confirmed' => $confirmed,
                    'paying' => $paying,
                    'amount' => $paying > 0 && $realAmount > 0 ? round($realAmount / $paying, 2) : $ticketOffer->price,
                    'payingAmount' => $amount,
                    'availableTickets' => $ticketOffer->availableTickets,
                ];
            }
        }

        return $computed;
    }

    /**
     * Deux lignes du récapitulatif pour les places à tarif spécial : une pour
     * les billetteries privées, une pour les tokens visiteurs.
     *
     * @return array<string, array<string, int|float|string|null>> Lignes du récapitulatif par catégorie
     */
    private function computeSpecialPriceStatistics(EventStats $statistics, Event $event): array
    {
        $specialPriceStats = $statistics->ticketType->specialPrice;
        $links = [
            SpecialPriceStats::BUCKET_BILLETTERIE_PRIVEE => [
                'label' => 'Billeterie privée',
                'url' => $this->generateUrl('admin_event_billeterie_privee', ['id' => $event->getId()]),
                'linkLabel' => 'Voir les billetteries privées',
            ],
            SpecialPriceStats::BUCKET_TOKEN_VISITEUR => [
                'label' => 'Tokens visiteurs',
                'url' => $this->generateUrl('admin_event_special_price', ['id' => $event->getId()]),
                'linkLabel' => 'Voir les tokens visiteurs',
            ],
        ];

        $computed = [];
        foreach ($links as $bucket => $link) {
            $registered = $specialPriceStats->registered[$bucket] ?? 0;

            if ($registered === 0) {
                continue;
            }

            $confirmed = $specialPriceStats->confirmed[$bucket] ?? 0;
            $paying = $specialPriceStats->paying[$bucket] ?? 0;
            $realAmount = $specialPriceStats->realAmounts[$bucket] ?? 0.0;

            // Plusieurs montants distincts dans cette catégorie : pas de prix unitaire trompeur,
            // on redirige vers la page correspondante
            $hasMultipleAmounts = ($specialPriceStats->distinctAmounts[$bucket] ?? 0) > 1;

            $computed[$bucket] = [
                'label' => $link['label'],
                'registered' => $registered,
                'confirmed' => $confirmed,
                'paying' => $paying,
                'amount' => $hasMultipleAmounts ? null : ($paying > 0 && $realAmount > 0 ? round($realAmount / $paying, 2) : 0.0),
                'payingAmount' => $realAmount,
                'availableTickets' => null,
                'linkUrl' => $link['url'],
                'linkLabel' => $link['linkLabel'],
            ];
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
