<?php

declare(strict_types=1);

namespace AppBundle\Association\UserMembership;

use AppBundle\Association\MemberType;
use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\User;
use AppBundle\MembershipFee\Model\MembershipFee;
use AppBundle\MembershipFee\Model\Repository\MembershipFeeRepository;
use CCMBenchmark\Ting\Repository\Collection;

class SeniorityComputer
{
    public function __construct(private readonly MembershipFeeRepository $membershipFeeRepository) {}

    /** @return array{years: int, first_year: int|null} */
    public function computeCompanyAndReturnInfos(CompanyMember $companyMember): array
    {
        $cotis = $this->membershipFeeRepository->getListByUserTypeAndId(MemberType::MemberCompany, $companyMember->getId());

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
     * @param Collection<MembershipFee> $cotisations
     * @return array{years: int, first_year: int|null}
     */
    private function computeFromCotisationsAndReturnInfos(Collection $cotisations): array
    {
        $now = new \DateTime();
        $diffs = [];

        $years = [];
        foreach ($cotisations as $coti) {
            $from = $coti->getStartDate();
            $to = $coti->getEndDate();
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
            $firstYear = (int) min($years);
        }

        return [
            'years' => $totalDiffs->y,
            'first_year' => $firstYear,
        ];
    }
}
