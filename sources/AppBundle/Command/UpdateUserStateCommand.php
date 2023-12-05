<?php

namespace AppBundle\Command;

use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateUserStateCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('update-user-state')

        ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->getContainer()->get('ting')->get(UserRepository::class);

        foreach ($userRepository->loadAll() as $user) {
            $hasUptoDateMembershipFee = $user->hasUpToDateMembershipFee();
            $user->setStatus($hasUptoDateMembershipFee ? User::STATUS_ACTIVE : User::STATUS_INACTIVE);
            $userRepository->save($user);
        }

        return 0;
    }
}
