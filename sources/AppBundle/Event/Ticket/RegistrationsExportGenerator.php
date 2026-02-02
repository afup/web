<?php

declare(strict_types=1);

namespace AppBundle\Event\Ticket;

use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use AppBundle\Association\UserMembership\SeniorityComputer;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Invoice;
use AppBundle\Event\Model\Repository\InvoiceRepository;
use AppBundle\Event\Model\Repository\TicketRepository;
use AppBundle\Event\Model\Ticket;
use AppBundle\Event\Model\TicketType;
use AppBundle\Offices\OfficeFinder;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

readonly class RegistrationsExportGenerator
{
    public function __construct(
        private OfficeFinder      $officeFinder,
        private SeniorityComputer $seniorityComputer,
        private TicketRepository  $ticketRepository,
        private InvoiceRepository $invoiceRepository,
        private UserRepository    $userRepository,
    ) {}

    public function export(Event $event, \SplFileObject $toFile): void
    {
        $columns = [
            'id',
            'reference',
            'prenom',
            'nom',
            'societe',
            'tags',
            'type_pass',
            'email',
            'member_since',
            'office',
        ];

        $toFile->fputcsv($columns);

        foreach ($this->getFromRegistrationsOnEvent($event) as $row) {
            $preparedRow = [];
            foreach ($columns as $column) {
                if (!array_key_exists($column, $row)) {
                    throw new \RuntimeException(sprintf('Colonne "%s" non trouvée : %s', $column, var_export($row, true)));
                }
                $preparedRow[] = $row[$column];
            }
            $toFile->fputcsv($preparedRow);
        }
    }

    /**
     * @return \Generator
     */
    protected function getFromRegistrationsOnEvent(Event $event)
    {
        $tickets = $this->ticketRepository->getByEvent($event);

        foreach ($tickets as $ticket) {
            $status = $ticket->getStatus();
            if (
                in_array($status, [Ticket::STATUS_CANCELLED, Ticket::STATUS_ERROR, Ticket::STATUS_DECLINED])
            ) {
                // On n'exporte pas les billets inscriptions annulées
                // ou en erreur de paiement / refusées
                continue;
            }

            $invoice = $this->invoiceRepository->getByReference($ticket->getReference());
            if (!$invoice instanceof Invoice) {
                continue;
            }

            try {
                $user = $this->userRepository->loadUserByUsername($ticket->getEmail());
            } catch (UserNotFoundException) {
                $user = null;
            }

            if (null === ($office = $ticket->getNearestOffice())) {
                $office = $this->officeFinder->findOffice($invoice, $user);
            }

            yield [
                'id' => $ticket->getId(),
                'reference' => $invoice->getReference(),
                'prenom' => $ticket->getFirstname(),
                'nom' => $ticket->getLastname(),
                'societe' => $invoice->getCompany(),
                'tags' => $this->extractAndCleanTags($ticket->getComments()),
                'type_pass' => $this->getTypePass($ticket),
                'email' => $ticket->getEmail(),
                'member_since' => null !== $user ? $this->computeSeniority($user) : null,
                'office' => $office,
                'distance' => null,
                'error' => null,
                'city' => null !== $user ? $user->getCity() : $invoice->getCity(),
                'zip_code' => null !== $user ? $user->getZipCode() : $invoice->getZipCode(),
                'country' => null !== $user ? $user->getCountry() : $invoice->getCountryId(),
            ];
        }
    }

    private function extractAndCleanTags(?string $comments): ?string
    {
        if (!$comments) {
            return null;
        }
        preg_match('@\<tag\>(.*)\</tags?\>@i', $comments, $matches);
        $tags =  $matches[1] ?? '';
        $tags = explode(';', $tags);

        return implode(' - ', array_filter($tags));
    }

    private function computeSeniority(User $user): int
    {
        return $this->seniorityComputer->compute($user);
    }

    private function getTypePass(TicketType $ticketType): string
    {
        $typePass = match ($ticketType->getId()) {
            Ticket::TYPE_ORGANIZATION => 'ORGANISATION',
            Ticket::TYPE_PRESS => 'PRESSE',
            Ticket::TYPE_SPEAKER => 'CONFERENCIER',
            Ticket::TYPE_SPONSOR => 'SPONSOR',
            default => false,
        };
        if ($typePass !== false) {
            return $typePass;
        }

        if ($ticketType->getDay() === 'one') {
            return 'PASS JOUR 1';
        }
        if ($ticketType->getDay() === 'two') {
            return 'PASS JOUR 2';
        }
        if ($ticketType->getDay() === 'one,two') {
            return 'PASS 2 JOURS';
        }

        throw new \RuntimeException(sprintf('Libellé du type %s non trouvé', var_export($ticketType, true)));
    }
}
