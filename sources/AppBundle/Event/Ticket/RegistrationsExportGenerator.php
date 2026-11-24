<?php

declare(strict_types=1);

namespace AppBundle\Event\Ticket;

use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use AppBundle\Association\UserMembership\SeniorityComputer;
use AppBundle\Event\Entity\Repository\TicketTypeRepository;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Invoice;
use AppBundle\Event\Model\Repository\InvoiceRepository;
use AppBundle\Event\Entity\Repository\InscriptionRepository;
use AppBundle\Event\Entity\TicketType;
use AppBundle\Offices\OfficeFinder;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use AppBundle\Event\Enum\InscriptionEtat;
use AppBundle\Event\Enum\TypeInscription;

readonly class RegistrationsExportGenerator
{
    public function __construct(
        private OfficeFinder $officeFinder,
        private SeniorityComputer $seniorityComputer,
        private InscriptionRepository $inscriptionRepository,
        private InvoiceRepository $invoiceRepository,
        private UserRepository $userRepository,
        private TicketTypeRepository $ticketTypeRepository,
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
            'date_inscription',
        ];

        $toFile->fputcsv($columns, escape: '\\');

        foreach ($this->getFromRegistrationsOnEvent($event) as $row) {
            $preparedRow = [];
            foreach ($columns as $column) {
                if (!array_key_exists($column, $row)) {
                    throw new \RuntimeException(sprintf('Colonne "%s" non trouvée : %s', $column, var_export($row, true)));
                }
                $preparedRow[] = $row[$column];
            }
            $toFile->fputcsv($preparedRow, escape: '\\');
        }
    }

    protected function getFromRegistrationsOnEvent(Event $event): \Generator
    {
        $aggregates = $this->ticketTypeRepository->enrichTicketAggregates($this->inscriptionRepository->getByEventWithAll(
            event: $event,
            search: null,
            sortKey: 'date',
            sortDirection: 'desc',
        ));

        foreach ($aggregates as $aggregate) {
            $ticket = $aggregate->ticket;
            $ticketType = $aggregate->ticketType;
            $status = $ticket->status;
            if (
                in_array($status, [InscriptionEtat::Annulee, InscriptionEtat::ErreurPaiement, InscriptionEtat::PaiementRefuse], true)
            ) {
                // On n'exporte pas les billets inscriptions annulées
                // ou en erreur de paiement / refusées
                continue;
            }

            $invoice = $this->invoiceRepository->getByReference($ticket->reference);
            if (!$invoice instanceof Invoice) {
                continue;
            }

            try {
                $user = $this->userRepository->loadUserByUsername($ticket->email);
            } catch (UserNotFoundException) {
                $user = null;
            }

            if (null === ($office = $ticket->nearestOffice)) {
                $office = $this->officeFinder->findOffice($invoice, $user);
            }

            yield [
                'id' => $ticket->id,
                'reference' => $invoice->getReference(),
                'prenom' => $ticket->firstname,
                'nom' => $ticket->lastname,
                'societe' => $invoice->getCompany(),
                'tags' => $this->extractAndCleanTags($ticket->comments),
                'type_pass' => $this->getTypePass($ticketType),
                'email' => $ticket->email,
                'member_since' => null !== $user ? $this->computeSeniority($user) : null,
                'office' => $office,
                'distance' => null,
                'error' => null,
                'city' => null !== $user ? $user->getCity() : $invoice->getCity(),
                'zip_code' => null !== $user ? $user->getZipCode() : $invoice->getZipCode(),
                'country' => null !== $user ? $user->getCountry() : $invoice->getCountryId(),
                'date_inscription' => $ticket->date->format('Y-m-d'),
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

    private function getTypePass(?TicketType $ticketType): string
    {
        if ($ticketType === null) {
            throw new \RuntimeException('Type de tarif de l\'inscription non trouvé');
        }

        $typePass = match ($ticketType->id) {
            TypeInscription::Organisation->value => 'ORGANISATION',
            TypeInscription::Presse->value => 'PRESSE',
            TypeInscription::Conferencier->value => 'CONFERENCIER',
            TypeInscription::Sponsor->value => 'SPONSOR',
            default => false,
        };
        if ($typePass !== false) {
            return $typePass;
        }

        if ($ticketType->day === 'one') {
            return 'PASS JOUR 1';
        }
        if ($ticketType->day === 'two') {
            return 'PASS JOUR 2';
        }
        if ($ticketType->day === 'one,two') {
            return 'PASS 2 JOURS';
        }

        throw new \RuntimeException(sprintf('Libellé du type %s non trouvé', var_export($ticketType, true)));
    }
}
