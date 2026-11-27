<?php

declare(strict_types=1);

namespace AppBundle\Accounting;

use AppBundle\Accounting\Entity\Repository\InvoicingRepository;
use AppBundle\Association\Entity\Repository\CompanyMemberRepository;
use AppBundle\Association\Model\Repository\SubscriptionRepository;
use AppBundle\Event\Model\Repository\InvoiceRepository;
use AppBundle\Event\Model\Repository\TicketRepository;

class SearchResultProvider
{
    public function __construct(
        private readonly SubscriptionRepository $subscriptionRepository,
        private readonly CompanyMemberRepository $companyMemberRepository,
        private readonly TicketRepository $ticketRepository,
        private readonly InvoiceRepository $invoiceRepository,
        private readonly InvoicingRepository $invoicingRepository,
    ) {}

    /** @return array<string, mixed> */
    public function getResultsForQuery(string $query): array
    {
        if ($query === '') {
            return [];
        }

        $companyMembers = $this->companyMemberRepository->searchCompanyMemberSubscriptions($query);
        $members = $this->subscriptionRepository->searchMemberSubscriptions($query);
        $eventsRegistrations = $this->ticketRepository->searchAllPastEvents($query);
        $eventsInvoices = $this->invoiceRepository->searchAllPastEventsInvoices($query);
        $invoices = $this->invoicingRepository->searchAllQuotesAndInvoices($query);

        return [
            'companyMembers' => $companyMembers,
            'members' => $members,
            'eventsRegistrations' => $eventsRegistrations,
            'eventsInvoices' => $eventsInvoices,
            'invoices' => $invoices,
        ];
    }
}
