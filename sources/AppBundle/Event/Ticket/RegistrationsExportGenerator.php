<?php

declare(strict_types=1);

namespace AppBundle\Event\Ticket;

use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use AppBundle\Association\UserMembership\SeniorityComputer;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\InvoiceRepository;
use AppBundle\Event\Model\Repository\TicketRepository;
use AppBundle\Event\Model\Ticket;
use AppBundle\Offices\OfficeFinder;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class RegistrationsExportGenerator
{
    public function __construct(
        private readonly OfficeFinder $officeFinder,
        private readonly SeniorityComputer $seniorityComputer,
        private readonly TicketRepository $ticketRepository,
        private readonly InvoiceRepository $invoiceRepository,
        private readonly UserRepository $userRepository,
    ) {
    }

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
                $status == Ticket::STATUS_CANCELLED
                ||  $status == Ticket::STATUS_ERROR
                ||  $status == Ticket::STATUS_DECLINED
            ) {
                // On n'exporte pas les billets inscriptions annulées
                // ou en erreur de paiement / refusées
                continue;
            }

            $invoice = $this->invoiceRepository->getByReference($ticket->getReference());

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
                'type_pass' => $this->getTypePass($ticket->getTicketTypeId()),
                'email' => $ticket->getEmail(),
                'member_since' => null !== $user ? $this->comptureSeniority($user) : null,
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

    private function comptureSeniority(User $user): int
    {
        return $this->seniorityComputer->compute($user);
    }

    private function getTypePass($type): string
    {
        return match ($type) {
            AFUP_FORUM_PREMIERE_JOURNEE, AFUP_FORUM_LATE_BIRD_PREMIERE_JOURNEE, Ticket::TYPE_AFUP_DAY_EARLY_BIRD, Ticket::TYPE_AFUP_DAY_CROISIERE, Ticket::TYPE_AFUP_DAY_LATE, Ticket::TYPE_AFUP_DAY_CFP_SUBMITTER, Ticket::TYPE_AFUP_DAY_LIVE_FREE, Ticket::TYPE_AFUP_DAY_LIVE_SOUTIEN_1, Ticket::TYPE_AFUP_DAY_LIVE_SOUTIEN_2, Ticket::TYPE_AFUP_DAY_LIVE_SOUTIEN_3, Ticket::TYPE_AFUP_DAY_LIVE_SOUTIEN_4, Ticket::TYPE_AFUP_DAY_2021_LIVE_1 => 'PASS JOUR 1',
            AFUP_FORUM_DEUXIEME_JOURNEE, AFUP_FORUM_LATE_BIRD_DEUXIEME_JOURNEE, Ticket::TYPE_AFUP_DAY_2021_LIVE_2 => 'PASS JOUR 2',
            AFUP_FORUM_2_JOURNEES, AFUP_FORUM_2_JOURNEES_AFUP, AFUP_FORUM_2_JOURNEES_ETUDIANT, AFUP_FORUM_2_JOURNEES_PREVENTE, AFUP_FORUM_2_JOURNEES_AFUP_PREVENTE, AFUP_FORUM_2_JOURNEES_ETUDIANT_PREVENTE, AFUP_FORUM_2_JOURNEES_COUPON, AFUP_FORUM_INVITATION, AFUP_FORUM_EARLY_BIRD, AFUP_FORUM_EARLY_BIRD_AFUP, AFUP_FORUM_LATE_BIRD, AFUP_FORUM_LATE_BIRD_AFUP, AFUP_FORUM_CFP_SUBMITTER, AFUP_FORUM_SPECIAL_PRICE, Ticket::TYPE_FORUM_PHP_LIVE_SOUTIEN_1, Ticket::TYPE_FORUM_PHP_LIVE_FREE, Ticket::TYPE_FORUM_PHP_LIVE_SOUTIEN_2, Ticket::TYPE_FORUM_PHP_LIVE_SOUTIEN_3, Ticket::TYPE_FORUM_PHP_LIVE_SOUTIEN_4, Ticket::TYPE_FORUM_PHP_LIVE_SOUTIEN_5, Ticket::TYPE_FORUM_PHP_LIVE_SOUTIEN_6, Ticket::TYPE_AFUP_DAY_2021_LIVE_3, Ticket::TYPE_AFUP_DAY_2021_LIVE_4 => 'PASS 2 JOURS',
            AFUP_FORUM_ORGANISATION => 'ORGANISATION',
            AFUP_FORUM_PRESSE => 'PRESSE',
            AFUP_FORUM_CONFERENCIER => 'CONFERENCIER',
            AFUP_FORUM_SPONSOR => 'SPONSOR',
            default => throw new \RuntimeException(sprintf('Libellé du type %s non trouvé', var_export($type, true))),
        };
    }
}
