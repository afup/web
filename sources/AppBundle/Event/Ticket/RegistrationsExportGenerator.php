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
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class RegistrationsExportGenerator
{
    private OfficeFinder $officeFinder;

    private SeniorityComputer $seniorityComputer;

    private InvoiceRepository $invoiceRepository;

    private TicketRepository $ticketRepository;

    private UserRepository $userRepository;

    public function __construct(OfficeFinder $officeFinder, SeniorityComputer $seniorityComputer, TicketRepository $ticketRepository, InvoiceRepository $invoiceRepository, UserRepository $userRepository)
    {
        $this->officeFinder = $officeFinder;
        $this->seniorityComputer = $seniorityComputer;
        $this->ticketRepository = $ticketRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->userRepository = $userRepository;
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
            'office'
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
            } catch (UsernameNotFoundException $exception) {
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
        switch ($type) {
            case AFUP_FORUM_PREMIERE_JOURNEE:
            case AFUP_FORUM_LATE_BIRD_PREMIERE_JOURNEE:
            case Ticket::TYPE_AFUP_DAY_EARLY_BIRD:
            case Ticket::TYPE_AFUP_DAY_CROISIERE:
            case Ticket::TYPE_AFUP_DAY_LATE:
            case Ticket::TYPE_AFUP_DAY_CFP_SUBMITTER:
            case Ticket::TYPE_AFUP_DAY_LIVE_FREE:
            case Ticket::TYPE_AFUP_DAY_LIVE_SOUTIEN_1:
            case Ticket::TYPE_AFUP_DAY_LIVE_SOUTIEN_2:
            case Ticket::TYPE_AFUP_DAY_LIVE_SOUTIEN_3:
            case Ticket::TYPE_AFUP_DAY_LIVE_SOUTIEN_4:
            case Ticket::TYPE_AFUP_DAY_2021_LIVE_1:
                return 'PASS JOUR 1';
            case AFUP_FORUM_DEUXIEME_JOURNEE:
            case AFUP_FORUM_LATE_BIRD_DEUXIEME_JOURNEE:
            case Ticket::TYPE_AFUP_DAY_2021_LIVE_2:
                return 'PASS JOUR 2';
            case AFUP_FORUM_2_JOURNEES:
            case AFUP_FORUM_2_JOURNEES_AFUP:
            case AFUP_FORUM_2_JOURNEES_ETUDIANT:
            case AFUP_FORUM_2_JOURNEES_PREVENTE:
            case AFUP_FORUM_2_JOURNEES_AFUP_PREVENTE:
            case AFUP_FORUM_2_JOURNEES_ETUDIANT_PREVENTE:
            case AFUP_FORUM_2_JOURNEES_COUPON:
            case AFUP_FORUM_INVITATION:
            case AFUP_FORUM_EARLY_BIRD:
            case AFUP_FORUM_EARLY_BIRD_AFUP:
            case AFUP_FORUM_LATE_BIRD:
            case AFUP_FORUM_LATE_BIRD_AFUP:
            case AFUP_FORUM_CFP_SUBMITTER:
            case AFUP_FORUM_SPECIAL_PRICE:
            case Ticket::TYPE_FORUM_PHP_LIVE_SOUTIEN_1:
            case Ticket::TYPE_FORUM_PHP_LIVE_FREE:
            case Ticket::TYPE_FORUM_PHP_LIVE_SOUTIEN_2:
            case Ticket::TYPE_FORUM_PHP_LIVE_SOUTIEN_3:
            case Ticket::TYPE_FORUM_PHP_LIVE_SOUTIEN_4:
            case Ticket::TYPE_FORUM_PHP_LIVE_SOUTIEN_5:
            case Ticket::TYPE_FORUM_PHP_LIVE_SOUTIEN_6:
            case Ticket::TYPE_AFUP_DAY_2021_LIVE_3:
            case Ticket::TYPE_AFUP_DAY_2021_LIVE_4:
                return 'PASS 2 JOURS';
            case AFUP_FORUM_ORGANISATION:
                return 'ORGANISATION';
            case AFUP_FORUM_PRESSE:
                return 'PRESSE';
            case AFUP_FORUM_CONFERENCIER:
                return 'CONFERENCIER';
            case AFUP_FORUM_SPONSOR:
                return 'SPONSOR';
            default:
                throw new \RuntimeException(sprintf('Libellé du type %s non trouvé', var_export($type, true)));
        }
    }
}
