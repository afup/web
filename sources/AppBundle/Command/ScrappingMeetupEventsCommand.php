<?php

declare(strict_types=1);

namespace AppBundle\Command;

use AppBundle\Event\Model\Repository\MeetupRepository;
use AppBundle\Indexation\Meetups\MeetupScraper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ScrappingMeetupEventsCommand extends ContainerAwareCommand
{
    use LockableTrait;

    /**
     * @see Command
     */
    protected function configure(): void
    {
        $this
            ->setName('scrapping-meetup-event')
            ->setAliases(['s-m-e'])
            ->setDescription('Récupère les évènements meetup AFUP sur meetup.com pour les afficher sur le site de l\'afup.')
        ;
    }

    /**
     *
     * @see Command
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Import des events meetups via scrapping de meetup.com');

        if (!$this->lock()) {
            $io->warning('La commande est déjà en cours d\'exécution dans un autre processus.');

            return 0;
        }

        try {
            $ting = $this->getContainer()->get('ting');
            $meetupScraper = new MeetupScraper();
            $meetups = $meetupScraper->getEvents();

            $meetupRepository = $ting->get(MeetupRepository::class);

            $emlementsLength = $this->countAllNestedElements($meetups);
            $io->progressStart($emlementsLength);
            foreach ($meetups as $antenneMeetups) {
                foreach ($antenneMeetups as $meetup) {
                    $io->progressAdvance();

                    $id =$meetup->getId();
                    $existingMeetup = $meetupRepository->get($id);
                    if (!$existingMeetup) {
                        $meetupRepository->save($meetup);
                    } else {
                        $io->note(sprintf('Meetup  id %d déjà en base.', $id));
                    }
                }
            }
            $io->progressFinish();
            $io->success('Terminé avec succès');
            return 1;
        } catch (\Exception $e) {
            throw new \Exception('Problème lors du scraping ou de la sauvegarde des évènements Meetup', $e->getCode(), $e);
        }
    }

    /**
     * Permet de faire un count sur un tableau multi-dimensionnel
     *
     * @param $array
     *
     * @return int
     */
    private function countAllNestedElements($array)
    {
        $count = 0;

        foreach ($array as $element) {
            if (is_array($element)) {
                // Si l'élément est un tableau, on appelle récursivement la fonction
                $count += $this->countAllNestedElements($element);
            } else {
                $count++;
            }
        }

        return $count;
    }
}
