<?php

declare(strict_types=1);

namespace AppBundle\Command;

use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\PlanningRepository;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Repository\TweetRepository;
use AppBundle\VideoNotifier\Runner;
use CCMBenchmark\TingBundle\Repository\RepositoryFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TwitterBotCommand extends Command
{
    private RepositoryFactory $ting;
    private \TwitterAPIExchange $twitterAPIExchange;

    public function __construct(RepositoryFactory $ting, \TwitterAPIExchange $twitterAPIExchange)
    {
        $this->ting = $ting;
        $this->twitterAPIExchange = $twitterAPIExchange;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('twitter-bot:run')
            ->addOption('event-path', null, InputOption::VALUE_REQUIRED);
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $runner = new Runner(
            $this->ting->get(PlanningRepository::class),
            $this->ting->get(TalkRepository::class),
            $this->ting->get(EventRepository::class),
            $this->ting->get(SpeakerRepository::class),
            $this->ting->get(TweetRepository::class),
            $this->twitterAPIExchange
        );
        $runner->execute($this->getEventFilter($input));

        return 0;
    }

    protected function getEventFilter(InputInterface $input)
    {
        if (null === ($eventPath = $input->getOption('event-path'))) {
            return null;
        }

        $event = $this->ting
            ->get(EventRepository::class)
            ->getByPath($eventPath)
        ;

        if (null === $event) {
            throw new \InvalidArgumentException("L'évènement sur lequel filter n'a pas été trouvé");
        }

        return $event;
    }
}
