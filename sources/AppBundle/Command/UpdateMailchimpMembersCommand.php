<?php

declare(strict_types=1);

namespace AppBundle\Command;

use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Mailchimp\Mailchimp;
use AppBundle\Mailchimp\Runner;
use CCMBenchmark\TingBundle\Repository\RepositoryFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateMailchimpMembersCommand extends Command
{
    public function __construct(
        private readonly Mailchimp $mailchimp,
        private readonly RepositoryFactory $ting,
        private readonly string $mailchimpMembersList,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('mailchimp:update-members')
            ->addOption('init', null, null, "Add all active members to the list")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /**
         * @var UserRepository $userRepository
         */
        $userRepository = $this->ting->get(UserRepository::class);

        $mailchimp = $this->mailchimp;

        $runner = new Runner(
            $mailchimp,
            $userRepository,
            $this->mailchimpMembersList
        );

        $errors = $input->getOption('init') === true ? $runner->initList() : $runner->updateList();
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

        return Command::SUCCESS;
    }
}
