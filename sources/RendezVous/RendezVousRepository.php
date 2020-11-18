<?php

namespace App\RendezVous;

interface RendezVousRepository
{
    /**
     * @param int $id
     *
     * @return RendezVous|null
     */
    public function find($id);

    /** @return RendezVous|null */
    public function findNext();

    /**
     * @return RendezVous[]
     * @phpstan-return iterable<RendezVous>
     */
    public function findAll();

    /** @param RendezVous $rendezVous */
    public function save($rendezVous);
}
