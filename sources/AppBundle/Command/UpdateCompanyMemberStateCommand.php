<?php

declare(strict_types=1);

namespace AppBundle\Command;

use AppBundle\Association\Enum\CompanyMemberStatus;
use AppBundle\Association\Entity\Repository\CompanyMemberRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCompanyMemberStateCommand extends Command
{
    public function __construct(private readonly CompanyMemberRepository $companyMemberRepository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('update-company-member-state')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->companyMemberRepository->loadAll() as $companyMember) {
            $companyMember->status = $companyMember->hasUpToDateMembershipFee() ? CompanyMemberStatus::Active : CompanyMemberStatus::Inactive;
            $this->companyMemberRepository->save($companyMember);
        }

        return Command::SUCCESS;
    }
}
