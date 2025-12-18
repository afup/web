<?php

declare(strict_types=1);

namespace AppBundle\Command;

use Symfony\Component\Console\Attribute\Option;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Mailchimp\Mailchimp;
use AppBundle\Mailchimp\Runner;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(name: 'mailchimp:update-members')]
final readonly class UpdateMailchimpMembersCommand
{
    public function __construct(
        #[Autowire('@app.mailchimp_api')]
        private Mailchimp $mailchimp,
        private UserRepository $userRepository,
        #[Autowire('%env(MAILCHIMP_MEMBERS_LIST)%')]
        private string $mailchimpMembersList,
    ) {}

    public function __invoke(
        OutputInterface $output,
        #[Option(description: 'Add all active members to the list')]
        bool $init = false,
    ): int {
        $mailchimp = $this->mailchimp;

        $runner = new Runner(
            $mailchimp,
            $this->userRepository,
            $this->mailchimpMembersList,
        );

        $errors = $init ? $runner->initList() : $runner->updateList();
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
