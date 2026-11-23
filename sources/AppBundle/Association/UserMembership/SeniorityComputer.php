<?php

declare(strict_types=1);

namespace AppBundle\Association\UserMembership;

use AppBundle\Association\MemberType;
use AppBundle\Association\Entity\CompanyMember;
use AppBundle\Association\Model\User;
use AppBundle\MembershipFee\Entity\Cotisation;
use AppBundle\MembershipFee\Entity\Repository\CotisationRepository;
use Psr\Clock\ClockInterface;

final readonly class SeniorityComputer
{
    public function __construct(
        private CotisationRepository $membershipFeeRepository,
        private ClockInterface $clock,
    ) {}

    /** @return array{years: int, first_year: int|null} */
    public function computeCompanyAndReturnInfos(CompanyMember $companyMember): array
    {
        $cotis = $this->membershipFeeRepository->getListByUserTypeAndId(MemberType::MemberCompany, $companyMember->id);

        return $this->computeFromCotisationsAndReturnInfos($cotis);
    }

    public function compute(User $user): int
    {
        $infos = $this->computeAndReturnInfos($user);

        return $infos['years'];
    }

    /** @return array{years: int, first_year: int|null} */
    public function computeAndReturnInfos(User $user): array
    {
        $cotis = $this->membershipFeeRepository->getListByUserTypeAndId(MemberType::MemberPhysical, $user->getId());

        return $this->computeFromCotisationsAndReturnInfos($cotis);
    }

    /**
     * @param Cotisation[] $cotisations
     * @return array{years: int, first_year: int|null}
     */
    private function computeFromCotisationsAndReturnInfos(array $cotisations): array
    {
        $now = $this->clock->now();
        $membershipYears = [];
        $startYears = [];

        foreach ($cotisations as $cotisation) {
            // Une cotisation correspond toujours à une année d'adhésion, comptée dès son premier jour.
            // On ne compte pas la durée d'une ligne de cotisation, pour éviter de compter les périodes de pause.
            // Une cotisation enregistrée plusieurs fois (même date de fin) ne compte qu'une fois.
            if ($cotisation->dateDebut !== null && $cotisation->dateFin !== null && $cotisation->dateDebut <= $now) {
                $membershipYears[$cotisation->dateFin->format('Y-m-d')] = true;
            }

            if ($cotisation->dateDebut !== null) {
                $startYears[] = (int) $cotisation->dateDebut->format('Y');
            }
        }

        return [
            'years' => count($membershipYears),
            'first_year' => $startYears === [] ? null : min($startYears),
        ];
    }
}
