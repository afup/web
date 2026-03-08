<?php

declare(strict_types=1);

namespace AppBundle\Accounting;

use AppBundle\Association\Model\Repository\CompanyMemberRepository;
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
    ) {}


    public function getResultsForQuery(string $query)
    {
        if ($query === '') {
            return [];
        }

        $companyMembers = $this->companyMemberRepository->searchCompanyMemberSubscriptions($query);
        $members = $this->subscriptionRepository->searchMemberSubscriptions($query);
        $eventsRegistrations = $this->ticketRepository->searchAllPastEvents($query);
        $eventsInvoices = $this->invoiceRepository->searchAllPastEventsInvoices($query);
        $invoices = $this->invoiceRepository->searchAllQuotesAndInvoices($query);

        return [
            'companyMembers' => $companyMembers,
            'members' => $members,
            'eventsRegistrations' => $eventsRegistrations,
            'eventsInvoices' => $eventsInvoices,
            'invoices' => $invoices,
        ];
    }
}
