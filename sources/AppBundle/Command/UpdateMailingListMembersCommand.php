<?php


namespace AppBundle\Command;


use Afup\Site\Association\Assemblee_Generale;
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
        $mailingListRepository = $this->getContainer()->get('app.mailing_list_repository');
        $groupsRepository = $this->getContainer()->get('app.group_repository');

        $output->writeln("Synchronisation Mailing Lists " . date('Y-m-d H:i:s'));

        $output->writeln(" - recuperation membres à jour de cotisation...");

        $assembly = $this->getContainer()->get('app.legacy_model_factory')->createObject(Assemblee_Generale::class);
        $membersAfup = explode(';', strtolower($assembly->obtenirListeEmailPersonnesAJourDeCotisation()));
        $membersAfup = array_map(function($email) use ($groupsRepository){
            return $groupsRepository->cleanEmail($email);
        }, $membersAfup);

        $filter = function (\Google_Service_Directory_Member $member) use ($groupsRepository, $membersAfup)
        {
            // Remove every mail if not a member
            return !in_array($member->getEmail(), $membersAfup);
        };

        $lists = $mailingListRepository->getAllMailingLists(true);

        foreach ($lists as $list)
        {
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
