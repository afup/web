<?php

declare(strict_types=1);

namespace AppBundle\Association\UserMembership;

use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;

class StatisticsComputer
{
    private UserRepository $userRepository;
    private CompanyMemberRepository $companyMemberRepository;

    public function __construct(UserRepository $userRepository, CompanyMemberRepository $companyMemberRepository)
    {
        $this->userRepository = $userRepository;
        $this->companyMemberRepository = $companyMemberRepository;
    }

    public function computeStatistics(): Statistics
    {
        $statistics = new Statistics();
        /** @var User[] $users */
        $users = $this->userRepository->getActiveMembers(UserRepository::USER_TYPE_ALL);
        foreach ($users as $user) {
            $statistics->usersCount++;
            if ($user->isMemberForCompany()) {
                $statistics->usersCountWithCompanies++;

                if (isset($companies[$user->getCompanyId()]) === false) {
                    $companies[$user->getCompanyId()] = true;
                    $statistics->companiesCountWithLinkedUsers++;
                }
            } else {
                $statistics->usersCountWithoutCompanies++;
            }
        }
        $statistics->companiesCount = $this->companyMemberRepository->countByStatus(CompanyMember::STATUS_ACTIVE);

        return $statistics;
    }
}
