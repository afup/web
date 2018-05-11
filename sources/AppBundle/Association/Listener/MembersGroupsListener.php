<?php

namespace AppBundle\Association\Listener;

use AppBundle\Association\Event\NewMemberEvent;
use AppBundle\Groups\GroupRepository;
use AppBundle\Groups\Model\MailingList;
use AppBundle\Groups\Model\Repository\MailingListRepository;
use Psr\Log\LoggerInterface;

class MembersGroupsListener
{
    /**
     * @var MailingListRepository
     */
    private $mailingListRepository;

    /**
     * @var GroupRepository
     */
    private $groupRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(MailingListRepository $mailingListRepository, GroupRepository $groupRepository, LoggerInterface $logger)
    {
        $this->mailingListRepository = $mailingListRepository;
        $this->groupRepository = $groupRepository;
        $this->logger = $logger;
    }

    public function onNewMemberEvent(NewMemberEvent $event)
    {
        // Premiere cotisation payée: il faut abonner le membre à toutes les ml définies par défaut
        /**
         * @var $lists MailingList[]
         */
        $lists = $this->mailingListRepository->getBy(['autoRegistration' => true]);

        foreach ($lists as $list) {
            try {
                $this->groupRepository->addMember($list->getEmail(), $event->getUser()->getEmail());
            } catch (\Google_Service_Exception $e) {
                $this->logger->error(
                    'Could not add a new member to a mailing list',
                    ['user' => $event->getUser()->getId(), 'mailing' => $list->getEmail()]
                );
            }
        }
    }
}
