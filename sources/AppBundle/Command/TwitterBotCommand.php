<?php

namespace AppBundle\Command;

use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\PlanningRepository;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Repository\TweetRepository;
use AppBundle\VideoNotifier\Runner;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TwitterBotCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this->setName('twitter-bot:run');
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $ting = $container->get('ting');
        $runner = new Runner(
            $ting->get(PlanningRepository::class),
            $ting->get(TalkRepository::class),
            $ting->get(EventRepository::class),
            $ting->get(SpeakerRepository::class),
            $ting->get(TweetRepository::class),
            $container->get('app.twitter_api')
        );
        $runner->execute();
    }
}
