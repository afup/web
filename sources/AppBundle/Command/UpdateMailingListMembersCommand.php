<?php

namespace AppBundle\Command;

use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use AppBundle\Groups\GroupRepository;
use Google_Service_Directory_Member;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateMailingListMembersCommand extends ContainerAwareCommand
{
    const MEMBERS_MAILING_ADDRESS = 'membres@afup.org';

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('groups:update-members')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $mailingListRepository = $this->getContainer()->get(\AppBundle\Groups\Model\Repository\MailingListRepository::class);
        $groupsRepository = $this->getContainer()->get(\AppBundle\Groups\GroupRepository::class);

        $output->writeln("Synchronisation Mailing Lists " . date('Y-m-d H:i:s'));

        $output->writeln(" - récupération des membres à jour de cotisation...");
        /**
         * @var $membersAfup User[]
         */
        $membersAfup = $this->getContainer()->get(\AppBundle\Association\Model\Repository\UserRepository::class)->getActiveMembers(UserRepository::USER_TYPE_ALL);

        $emails = [];
        foreach ($membersAfup as $member) {
            $emails[] = $groupsRepository->cleanEmail($member->getEmail());
        }

        $filter = function (\Google_Service_Directory_Member $member) use ($groupsRepository, $emails) {
            // Remove every mail if not a member
            return !in_array($member->getEmail(), $emails);
        };

        $this->addMissingMembers($groupsRepository, $emails, $output);

        $lists = $mailingListRepository->getAllMailingLists(true);

        foreach ($lists as $list) {
            $output->writeln($list->getEmail());
            $membersOfList = $groupsRepository->getMembers($list->getEmail());
            $membersOfListNonMemberAfup = array_filter($membersOfList, $filter);

            // Get expired members, which are still member of a mailing list
            foreach ($membersOfListNonMemberAfup as $member) {
                $output->write(sprintf('Removing "%s"', $member->getEmail()));
                if ($groupsRepository->removeMember($list->getEmail(), $member->getEmail()) !== false) {
                    $output->writeln('[OK]');
                } else {
                    $output->writeln('[NOK]');
                }
            }
        }
    }

    private function addMissingMembers(GroupRepository $groupsRepository, $membersAfupEmails, OutputInterface $output)
    {
        $membersOfList = $groupsRepository->getMembers(self::MEMBERS_MAILING_ADDRESS);

        $listEmails = [];
        /** @var Google_Service_Directory_Member $memberOfList */
        foreach ($membersOfList as $memberOfList) {
            $listEmails[$memberOfList->getEmail()] = true;
        }

        foreach ($membersAfupEmails as $memberEmail) {
            if (!isset($listEmails[$memberEmail])) {
                $output->write(sprintf('Adding "%s"', $memberEmail));
                if ($groupsRepository->addMember(self::MEMBERS_MAILING_ADDRESS, $memberEmail) !== false) {
                    $output->writeln('[OK]');
                } else {
                    $output->writeln('[NOK]');
                }
            }
        }
    }
}
