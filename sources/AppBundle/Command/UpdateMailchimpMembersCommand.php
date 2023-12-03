<?php

namespace AppBundle\Command;

use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Mailchimp\Runner;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateMailchimpMembersCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('mailchimp:update-members')
            ->addOption('init', null, null, "Add all active members to the list")
        ;
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $ting = $container->get('ting');

        $membersListId = $this->getContainer()->getParameter('mailchimp_members_list');

        /**
         * @var $userRepository UserRepository
         */
        $userRepository = $ting->get(UserRepository::class);

        $mailchimp = $this->getContainer()->get(\AppBundle\Mailchimp\Mailchimp::class);

        $runner = new Runner(
            $mailchimp,
            $userRepository,
            $membersListId
        );

        if ($input->getOption('init') === true) {
            $errors = $runner->initList();
        } else {
            $errors = $runner->updateList();
        }
        if ($errors !== []) {
            $table = new Table($output);
            $table
                ->setHeaders(['email', 'erreur'])
                ->setRows($errors)
                ->render()
            ;
        } else {
            $output->writeln("Pas d'erreur durant le traitement");
        }

        return 0;
    }
}
