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

    public function computeCompany(CompanyMember $companyMember)
    {
        $cotis = $this->membershipFeeRepository->getListByUserTypeAndId(MemberType::MemberCompany, $companyMember->getId());

        $infos = $this->computeFromCotisationsAndReturnInfos($cotis);

        return $infos['years'];
    }

    public function computeCompanyAndReturnInfos(CompanyMember $companyMember): array
    {
        $cotis = $this->membershipFeeRepository->getListByUserTypeAndId(MemberType::MemberCompany, $companyMember->getId());

        return $this->computeFromCotisationsAndReturnInfos($cotis);
    }

    public function compute(User $user)
    {
        $infos = $this->computeAndReturnInfos($user);

        return $infos['years'];
    }

    public function computeAndReturnInfos(User $user): array
    {
        $cotis = $this->membershipFeeRepository->getListByUserTypeAndId(MemberType::MemberPhysical, $user->getId());

        return $this->computeFromCotisationsAndReturnInfos($cotis);
    }

    /**
     * @param Collection<MembershipFee> $cotisations
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
            $firstYear = min($years);
        }

        return [
            'years' => $totalDiffs->y,
            'first_year' => $firstYear,
        ];
    }
}
