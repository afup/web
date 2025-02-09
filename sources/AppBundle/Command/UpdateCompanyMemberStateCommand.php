<?php

declare(strict_types=1);

namespace AppBundle\Command;

use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCompanyMemberStateCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure(): void
    {
        $this
            ->setName('update-company-member-state')

        ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var CompanyMemberRepository $companyMemberRepository */
        $companyMemberRepository = $this->getContainer()->get('ting')->get(CompanyMemberRepository::class);

        /** @var CompanyMember $companyMember */
        foreach ($companyMemberRepository->loadAll() as $companyMember) {
            $hasUptoDateMembershipFee = $companyMember->hasUpToDateMembershipFee();
            $companyMember->setStatus($hasUptoDateMembershipFee ? 1 : 0);
            $companyMemberRepository->save($companyMember);
        }

        return 0;
    }
}
