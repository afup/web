<?php

declare(strict_types=1);


namespace AppBundle\Command;

use AppBundle\Association;
use AppBundle\Association\CompanyMembership\CompanyReminderFactory;
use AppBundle\Association\MembershipReminderInterface;
use AppBundle\Association\Model\Repository\SubscriptionReminderLogRepository;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\UserMembership\Reminder15DaysAfterEnd;
use AppBundle\Association\UserMembership\Reminder15DaysBeforeEnd;
use AppBundle\Association\UserMembership\Reminder7DaysBeforeEnd;
use AppBundle\Association\UserMembership\ReminderDDay;
use AppBundle\Association\UserMembership\UserReminderFactory;
use AppBundle\Email\Mailer\Mailer;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use CCMBenchmark\TingBundle\Repository\RepositoryFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SubscriptionReminderCommand extends Command
{
    public function __construct(
        private readonly Mailer $mailer,
        private readonly RepositoryFactory $ting,
    ) {
        parent::__construct();
    }
    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this
            ->setName('subscription:reminder')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Do not send any mail, just show expiring subscriptions')
            ->setDescription('Remind any soon expiring (or just expired) subscriptions by sending mails.')
        ;
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $mailer = $this->mailer;
        $factory = new UserReminderFactory($mailer, $this->ting->get(SubscriptionReminderLogRepository::class));
        $companyFactory = new CompanyReminderFactory(
            $mailer,
            $this->ting->get(SubscriptionReminderLogRepository::class)
        );

        /**
         * @var UserRepository $repository
         */
        $repository = $this->ting->get(UserRepository::class);

        $dryRun = $input->getOption('dry-run');

        $today = new \DateTimeImmutable();

        $reminders = [
            'in 15 days' => [
                'date' => $today->add(new \DateInterval('P15D')),
                'physical' => Reminder15DaysBeforeEnd::class,
                'company' => Association\CompanyMembership\Reminder15DaysBeforeEnd::class,
            ],
            'in 7 days' => [
                'date' => $today->add(new \DateInterval('P7D')),
                'physical' => Reminder7DaysBeforeEnd::class,
                'company' => Association\CompanyMembership\Reminder7DaysBeforeEnd::class,
            ],
            'Today' => [
                'date' => $today,
                'physical' => ReminderDDay::class,
                'company' => Association\CompanyMembership\ReminderDDay::class,
            ],
            '15 days ago' => [
                'date' => $today->sub(new \DateInterval('P15D')),
                'physical' => Reminder15DaysAfterEnd::class,
                'company' => Association\CompanyMembership\Reminder15DaysAfterEnd::class,
            ],
        ];

        $output->writeln('<info>Reminders des souscriptions</info>');
        foreach ($reminders as $name => $details) {
            $reminder = $factory->getReminder($details['physical']);
            $users = $repository->getUsersByEndOfMembership($details['date'], UserRepository::USER_TYPE_PHYSICAL);

            $output->writeln(sprintf('%s (%s)', $name, $details['date']->format('d/m/Y')));
            $output->writeln(sprintf('<info>%s membres</info>', $users->count()));
            $this->handleReminders($output, $reminder, $users, $dryRun);


            $reminder = $companyFactory->getReminder($details['company']);
            $users = $repository->getUsersByEndOfMembership($details['date'], UserRepository::USER_TYPE_COMPANY);
            $output->writeln(sprintf('<info>%s entreprises</info>', $users->count()));
            $this->handleReminders($output, $reminder, $users, $dryRun);
        }

        return Command::SUCCESS;
    }

    private function handleReminders(
        OutputInterface $output,
        MembershipReminderInterface $reminder,
        CollectionInterface $users,
        $dryRun = true,
    ): void {
        foreach ($users as $user) {
            /**
             * @var $user Association\Model\User
             */
            $output->writeln($user->getEmail());
            if ($dryRun === false) {
                $reminder->sendReminder($user);
            }
        }
    }
}
