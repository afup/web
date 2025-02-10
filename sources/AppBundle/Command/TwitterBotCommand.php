<?php

declare(strict_types=1);

namespace AppBundle\Command;

use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\PlanningRepository;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Repository\TweetRepository;
use AppBundle\VideoNotifier\Runner;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TwitterBotCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure(): void
    {
        $this
            ->setName('twitter-bot:run')
            ->addOption('event-path', null, InputOption::VALUE_REQUIRED);
        ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $container = $this->getContainer();
        $ting = $container->get('ting');
        $runner = new Runner(
            $ting->get(PlanningRepository::class),
            $ting->get(TalkRepository::class),
            $ting->get(EventRepository::class),
            $ting->get(SpeakerRepository::class),
            $ting->get(TweetRepository::class),
            $container->get(\TwitterAPIExchange::class)
        );
        $runner->execute($this->getEventFilter($input));

        return 0;
    }

    protected function getEventFilter(InputInterface $input)
    {
        if (null === ($eventPath = $input->getOption('event-path'))) {
            return null;
        }

        $event = $this
            ->getContainer()
            ->get('ting')
            ->get(EventRepository::class)
            ->getByPath($eventPath)
        ;

        if (null === $event) {
            throw new \InvalidArgumentException("L'évènement sur lequel filter n'a pas été trouvé");
        }

        return $event;
    }
}
