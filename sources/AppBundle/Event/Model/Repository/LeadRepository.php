<?php

namespace AppBundle\Event\Model\Repository;

use AppBundle\Event\Model\Lead;

use Trello\Manager;

class LeadRepository
{
    /**
     * @var Manager
     */
    private $manager;

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    public function save(Lead $lead)
    {
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
