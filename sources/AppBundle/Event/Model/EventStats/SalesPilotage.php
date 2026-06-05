<?php

declare(strict_types=1);

namespace AppBundle\Event\Model\EventStats;

final readonly class SalesPilotage
{
    public function __construct(
        /** Billets payants (type tarifaire payant) effectivement payés à ce jour */
        public int $paidTickets,
        /** Capacité de l'événement (nb_places) */
        public ?int $seats,
        /** Nombre de jours de vente restants avant la clôture des ventes */
        public int $daysToEndOfSales,
        /** Billets payants vendus par l'édition N-1 au même stade du cycle de vente */
        public ?int $previousPaidTicketsAtSameStage,
        /** Billets payants vendus par l'édition N-1 au total (en fin de vente) */
        public ?int $previousPaidTicketsTotal,
        /** Titre de l'édition N-1 utilisée pour la comparaison */
        public ?string $previousEditionTitle,
    ) {}

    public function getFillRate(): ?int
    {
        if ($this->seats === null || $this->seats === 0) {
            return null;
        }

        return (int) floor($this->paidTickets * 100 / $this->seats);
    }

    public function getEvolutionRate(): ?int
    {
        if (empty($this->previousPaidTicketsAtSameStage)) {
            return null;
        }

        return (int) round(($this->paidTickets - $this->previousPaidTicketsAtSameStage) * 100 / $this->previousPaidTicketsAtSameStage);
    }
}
