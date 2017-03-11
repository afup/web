<?php


namespace AppBundle\Command;

use Afup\Site\Utils\Mail;
use AppBundle\Association\Model\Repository\SubscriptionReminderLogRepository;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use AppBundle\Association\UserMembership\Reminder15DaysAfterEnd;
use AppBundle\Association\UserMembership\Reminder15DaysBeforeEnd;
use AppBundle\Association\UserMembership\Reminder7DaysBeforeEnd;
use AppBundle\Association\UserMembership\ReminderDDay;
use AppBundle\Association\UserMembership\UserReminderFactory;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SubscriptionReminderCommand extends ContainerAwareCommand
{
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
        $factory = new UserReminderFactory((new Mail()), $this->getContainer()->get('ting')->get(SubscriptionReminderLogRepository::class));

        /**
         * @var $repository UserRepository
         */
        $repository = $this->getContainer()->get('ting')->get(UserRepository::class);

        $dryRun = $input->getOption('dry-run');

        $today = new \DateTimeImmutable();

        $reminders = [
            'in 15 days' => [
                'date' => $today->add(new \DateInterval('P15D')),
                'class' => Reminder15DaysBeforeEnd::class
            ],
            'in 7 days' => [
                'date' => $today->add(new \DateInterval('P7D')),
                'class' => Reminder7DaysBeforeEnd::class
            ],
            'Today' => [
                'date' => $today,
                'class' => ReminderDDay::class
            ],
            '15 days ago' => [
                'date' => $today->sub(new \DateInterval('P15D')),
                'class' => Reminder15DaysAfterEnd::class
            ],
        ];

        $output->writeln('<info>Reminders des souscriptions des personnes physiques</info>');
        foreach ($reminders as $name => $details) {
            $reminder = $factory->getReminder($details['class']);

            $users = $repository->getUsersByEndOfMembership($details['date']);
            $output->writeln(sprintf('%s (%s)', $name, $details['date']->format('d/m/Y')));
            $output->writeln(sprintf('<info>%s membres</info>', $users->count()));
            foreach ($users as $user) {
                /**
                 * @var $user User
                 */
                $output->writeln($user->getEmail());
                if ($dryRun === false) {
                    $reminder->sendReminder($user);
                }
            }
        }
    }
}
