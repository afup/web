<?php

namespace AppBundle\Event;

use AppBundle\Event\Model\Repository\TicketRepository;
use AppBundle\Event\Model\Ticket;

class AnonymousExport
{
    /**
     * @var TicketRepository
     */
    private $ticketRepository;

    public function __construct(TicketRepository $ticketRepository)
    {
        $this->ticketRepository = $ticketRepository;
    }

    /**
     * @return array
     */
    public function exportData()
    {
        /**
         * @var $rawData Ticket[]
         */
        $rawData = $this->ticketRepository->getAllTicketsForExport();
        $output = [];

        $transliterator = \Transliterator::createFromRules(':: NFD; :: [:Nonspacing Mark:] Remove; :: NFC;', \Transliterator::FORWARD);

        foreach ($rawData as $ticket) {
            // Remove spaces
            $label = str_replace(' ', '', $ticket->getLabel());

            // Remove accented chars
            $label = $transliterator->transliterate($label);

            // Harmonize case and create a hash
            $label = sha1(strtolower($label));

            $output[] = [
                'label' => $label,
                'event' => $ticket->getForumId()
            ];
        }
        return $output;
    }
}
