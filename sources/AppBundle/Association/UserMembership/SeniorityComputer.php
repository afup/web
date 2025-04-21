<?php

declare(strict_types=1);

namespace AppBundle\Association\UserMembership;

use Afup\Site\Association\Cotisations;
use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\User;

class SeniorityComputer
{
    private Cotisations $cotisations;

    public function __construct(Cotisations $cotisations)
    {
        $this->cotisations = $cotisations;
    }

    public function computeCompany(CompanyMember $companyMember)
    {
        $cotis = $this->cotisations->obtenirListe(AFUP_PERSONNES_MORALES, $companyMember->getId());

        $infos = $this->computeFromCotisationsAndReturnInfos($cotis);

        return $infos['years'];
    }

    public function computeCompanyAndReturnInfos(CompanyMember $companyMember): array
    {
        $cotis = $this->cotisations->obtenirListe(AFUP_PERSONNES_MORALES, $companyMember->getId());

        return $this->computeFromCotisationsAndReturnInfos($cotis);
    }

    public function compute(User $user)
    {
        $infos = $this->computeAndReturnInfos($user);

        return $infos['years'];
    }

    public function computeAndReturnInfos(User $user): array
    {
        $cotis = $this->cotisations->obtenirListe(AFUP_PERSONNES_PHYSIQUES, $user->getId());

        return $this->computeFromCotisationsAndReturnInfos($cotis);
    }

    private function computeFromCotisationsAndReturnInfos(array $cotisations): array
    {
        $now = new \DateTime();
        $diffs = [];

        $years = [];
        foreach ($cotisations as $coti) {
            $from = new \DateTimeImmutable('@' . $coti['date_debut']);
            $to = new \DateTimeImmutable('@' . $coti['date_fin']);
            $to = min($now, $to);
            $diffs[] = $from->diff($to);
            $years[] = $from->format('Y');
        }

        $reference = new \DateTimeImmutable();
        $lastest = clone $reference;
        foreach ($diffs as $dif) {
            $lastest = $lastest->add($dif);
        }

        $totalDiffs = $reference->diff($lastest);

        $firstYear = null;

        if ($years !== []) {
            $firstYear = min($years);
        }

        return [
            'years' => $totalDiffs->y,
            'first_year' => $firstYear,
        ];
    }
}
