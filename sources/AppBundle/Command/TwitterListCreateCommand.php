<?php

namespace AppBundle\Command;

use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Twitter\ListCreator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TwitterListCreateCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('twitter-list:create')
            ->addArgument('event-path', null, InputArgument::REQUIRED)
            ->addOption('custom-name', null, InputOption::VALUE_REQUIRED, 'Use this value as list name instead of the event title')
        ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $ting = $container->get('ting');
        $event = $this->getEventFilter($input);

        $twitterListCreator = new ListCreator($container->get(\TwitterAPIExchange::class), $ting->get(SpeakerRepository::class));
        $twitterListCreator->create($event, $input->getOption('custom-name'));

        return 0;
    }

    /**
     * @param InputInterface $input
     *
     * @return null
     */
    protected function getEventFilter(InputInterface $input)
    {
        $eventPath = $input->getArgument('event-path');
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
