<?php

namespace App\RendezVous;

use CCMBenchmark\Ting\Repository\CollectionInterface;

interface RendezVousSlideRepository
{
    /**
     * @param int $id
     *
     * @return RendezVousSlide|null
     */
    public function find($id);

    /** @return CollectionInterface&RendezVousSlide[] */
    public function findByRendezVous(RendezVous $rendezVous);

    public function deleteByRendezVous(RendezVous $rendezVous);

    /** @param RendezVousSlide $slide */
    public function save($slide);
}
