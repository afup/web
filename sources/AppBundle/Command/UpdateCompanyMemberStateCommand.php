<?php

declare(strict_types=1);

namespace AppBundle\Command;

use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use CCMBenchmark\TingBundle\Repository\RepositoryFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCompanyMemberStateCommand extends Command
{
    public function __construct(private readonly RepositoryFactory $ting)
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
        /** @var CompanyMemberRepository $companyMemberRepository */
        $companyMemberRepository = $this->ting->get(CompanyMemberRepository::class);

        /** @var CompanyMember $companyMember */
        foreach ($companyMemberRepository->loadAll() as $companyMember) {
            $hasUptoDateMembershipFee = $companyMember->hasUpToDateMembershipFee();
            $companyMember->setStatus($hasUptoDateMembershipFee ? 1 : 0);
            $companyMemberRepository->save($companyMember);
        }

        return Command::SUCCESS;
    }
}
