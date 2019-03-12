<?php


namespace AppBundle\Command;

use Afup\Site\Association\Assemblee_Generale;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateMailingListMembersCommand extends ContainerAwareCommand
{
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

        $assembly = $this->getContainer()->get(\AppBundle\LegacyModelFactory::class)->createObject(Assemblee_Generale::class);
        /**
         * @var $membersAfup User[]
         */
        $membersAfup = $this->getContainer()->get('app.user_repository')->getActiveMembers(UserRepository::USER_TYPE_ALL);

        $emails = [];
        foreach ($membersAfup as $member) {
            $emails[] = $groupsRepository->cleanEmail($member->getEmail());
        }

        $filter = function (\Google_Service_Directory_Member $member) use ($groupsRepository, $emails) {
            // Remove every mail if not a member
            return !in_array($member->getEmail(), $emails);
        };

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
}
