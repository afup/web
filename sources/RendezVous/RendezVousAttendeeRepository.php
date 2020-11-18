<?php

namespace App\RendezVous;

interface RendezVousAttendeeRepository
{
    /**
     * @param int $id
     *
     * @return RendezVousAttendee|null
     */
    public function find($id);

    /**
     * @return RendezVousAttendee[]
     * @phpstan-return iterable<RendezVousAttendee>
     */
    public function findComingUnconfirmed(RendezVous $rendezVous);

    public function refuseDeclinedInvitations(RendezVous $rendezVous);

    public function fillFreeSpotsWithPending(RendezVous $rendezVous);

    /**
     * @return RendezVousAttendee[]
     * @phpstan-return iterable<RendezVousAttendee>
     */
    public function findByRendezVous(RendezVous $rendezVous);

    /**
     * @return RendezVousAttendee[]
     * @phpstan-return iterable<RendezVousAttendee>
     */
    public function findComingAndPendingByRendezVous(RendezVous $rendezVous);

    /** @return int */
    public function countComing(RendezVous $rendezVous);

    /** @return int */
    public function countPending(RendezVous $rendezVous);

    /** @return RendezVousAttendee|null */
    public function findOneByHash($hash);

    /** @param RendezVousAttendee $attendee */
    public function delete($attendee);

    /** @param RendezVousAttendee $attendee */
    public function save($attendee);
}
