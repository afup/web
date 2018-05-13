<?php

namespace AppBundle\Event\Ticket;

use Afup\Site\Association\Cotisations;
use AppBundle\Association\Model\User;

class SeniorityComputer
{
    /**
     * @var Cotisations
     */
    private $cotisations;

    /**
     * @param Cotisations $cotisations
     */
    public function __construct(Cotisations $cotisations)
    {
        $this->cotisations = $cotisations;
    }

    /**
     * @param User $user
     *
     * @return \DateInterval
     */
    public function computeSeniority(User $user)
    {
        $typePersonne = $user->isMemberForCompany() ? AFUP_PERSONNES_MORALES : AFUP_PERSONNES_PHYSIQUES;
        $cotis = $this->cotisations->obtenirListe($typePersonne, $user->getId());
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

        return $totalDiffs;
    }
}
