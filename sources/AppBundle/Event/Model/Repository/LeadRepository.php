<?php

namespace AppBundle\Event\Model\Repository;

use AppBundle\Event\Model\Lead;

use Psr\Log\LoggerInterface;
use Trello\Manager;

class LeadRepository
{
    /**
     * @var Manager
     */
    private $manager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(Manager $manager, LoggerInterface $logger)
    {
        $this->manager = $manager;
        $this->logger = $logger;
    }

    public function save(Lead $lead)
    {
        $this->logger->info(sprintf('Lead collected and sent to trello: %s', json_encode($lead)));

        $card = $this->manager->getCard();
        $card
            ->setDueDate((new \DateTime())->add(new \DateInterval('P1W')))
            ->setName($lead->getCompany())
            ->setDescription(
                sprintf(
                    "%s %s \n%s - %s \n %s - %s \n %s",
                    $lead->getFirstname(),
                    $lead->getLastname(),
                    $lead->getPosition(),
                    $lead->getCompany(),
                    $lead->getPhone(),
                    $lead->getWebsite(),
                    $lead->getLanguage()
                )
            )
            ->setListId($lead->getEvent()->getTrelloListId())
            ->save()
        ;
    }
}
