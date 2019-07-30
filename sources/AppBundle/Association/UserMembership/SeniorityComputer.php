<?php

namespace AppBundle\Association\UserMembership;

use Afup\Site\Association\Cotisations;
use AppBundle\Association\Model\User;

class SeniorityComputer
{
    /**
     * @var Cotisations
     */
    private $cotisations;

    public function __construct(Cotisations $cotisations)
    {
        $this->cotisations = $cotisations;
    }

    public function compute(User $user)
    {
        $cotis = $this->cotisations->obtenirListe(AFUP_PERSONNES_PHYSIQUES, $user->getId());
        $now = new \DateTime();
        $diffs = [];

        foreach ($cotis as $coti) {
            $from = \DateTimeImmutable::createFromFormat('U', $coti['date_debut']);
            $to = \DateTimeImmutable::createFromFormat('U', $coti['date_fin']);
            $to = min($now, $to);
            $diffs[] = $from->diff($to);
        }

        $reference = new \DateTimeImmutable();
        $lastest = clone $reference;
        foreach ($diffs as $dif) {
            $lastest = $lastest->add($dif);
        }

        $totalDiffs = $reference->diff($lastest);

        return $totalDiffs->y;
    }
}
