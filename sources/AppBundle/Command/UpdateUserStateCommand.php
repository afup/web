<?php

declare(strict_types=1);

namespace AppBundle\Command;

use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use CCMBenchmark\TingBundle\Repository\RepositoryFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateUserStateCommand extends Command
{
    public function __construct(private readonly RepositoryFactory $ting)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('update-user-state')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->ting->get(UserRepository::class);

        foreach ($userRepository->loadAll() as $user) {
            $hasUptoDateMembershipFee = $user->hasUpToDateMembershipFee();
            $user->setStatus($hasUptoDateMembershipFee ? User::STATUS_ACTIVE : User::STATUS_INACTIVE);
            $userRepository->save($user);
        }

        return Command::SUCCESS;
    }
}
