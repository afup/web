<?php


namespace AppBundle\Command;

use Afup\Site\Utils\Mail;
use AppBundle\Association;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SubscriptionReminderCommand extends ContainerAwareCommand
{
    private $mailer;

    public function __construct(Mail $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @inheritDoc
     */
    protected function configure()
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
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $factory = new Association\UserMembership\UserReminderFactory($this->mailer, $this->getContainer()->get('ting')->get(Association\Model\Repository\SubscriptionReminderLogRepository::class));
        $companyFactory = new Association\CompanyMembership\CompanyReminderFactory(
            $this->mailer,
            $this->getContainer()->get('ting')->get(Association\Model\Repository\SubscriptionReminderLogRepository::class)
        );

        /**
         * @var $repository Association\Model\Repository\UserRepository
         */
        $repository = $this->getContainer()->get('ting')->get(Association\Model\Repository\UserRepository::class);

        $dryRun = $input->getOption('dry-run');

        $today = new \DateTimeImmutable();

        $reminders = [
            'in 15 days' => [
                'date' => $today->add(new \DateInterval('P15D')),
                'physical' => Association\UserMembership\Reminder15DaysBeforeEnd::class,
                'company' => Association\CompanyMembership\Reminder15DaysBeforeEnd::class
            ],
            'in 7 days' => [
                'date' => $today->add(new \DateInterval('P7D')),
                'physical' => Association\UserMembership\Reminder7DaysBeforeEnd::class,
                'company' => Association\CompanyMembership\Reminder7DaysBeforeEnd::class
            ],
            'Today' => [
                'date' => $today,
                'physical' => Association\UserMembership\ReminderDDay::class,
                'company' => Association\CompanyMembership\ReminderDDay::class
            ],
            '15 days ago' => [
                'date' => $today->sub(new \DateInterval('P15D')),
                'physical' => Association\UserMembership\Reminder15DaysAfterEnd::class,
                'company' => Association\CompanyMembership\Reminder15DaysAfterEnd::class
            ],
        ];

        $output->writeln('<info>Reminders des souscriptions</info>');
        foreach ($reminders as $name => $details) {
            $reminder = $factory->getReminder($details['physical']);
            $users = $repository->getUsersByEndOfMembership($details['date'], Association\Model\Repository\UserRepository::USER_TYPE_PHYSICAL);

            $output->writeln(sprintf('%s (%s)', $name, $details['date']->format('d/m/Y')));
            $output->writeln(sprintf('<info>%s membres</info>', $users->count()));
            $this->handleReminders($output, $reminder, $users, $dryRun);


            $reminder = $companyFactory->getReminder($details['company']);
            $users = $repository->getUsersByEndOfMembership($details['date'], Association\Model\Repository\UserRepository::USER_TYPE_COMPANY);
            $output->writeln(sprintf('<info>%s entreprises</info>', $users->count()));
            $this->handleReminders($output, $reminder, $users, $dryRun);
        }
    }

    private function handleReminders(
        OutputInterface $output,
        Association\MembershipReminderInterface $reminder,
        CollectionInterface $users,
        $dryRun = true
    ) {
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
