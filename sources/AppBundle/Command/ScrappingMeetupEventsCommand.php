<?php

declare(strict_types=1);

namespace AppBundle\Command;

use AppBundle\Event\Model\Repository\MeetupRepository;
use AppBundle\Indexation\Meetups\MeetupClient;
use CCMBenchmark\TingBundle\Repository\RepositoryFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ScrappingMeetupEventsCommand extends Command
{
    use LockableTrait;

    private RepositoryFactory $ting;
    private MeetupClient $meetupClient;

    public function __construct(RepositoryFactory $ting,
                                MeetupClient $meetupClient)
    {
        parent::__construct();
        $this->ting = $ting;
        $this->meetupClient = $meetupClient;
    }

    protected function configure(): void
    {
        $this
            ->setName('scrapping-meetup-event')
            ->setAliases(['s-m-e'])
            ->setDescription('Récupère les évènements meetup AFUP sur meetup.com pour les afficher sur le site de l\'afup.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Import des events meetups via scrapping de meetup.com');

        if (!$this->lock()) {
            $io->warning('La commande est déjà en cours d\'exécution dans un autre processus.');

            return 0;
        }

        try {
            $meetups = $this->meetupClient->getEvents();

            $meetupRepository = $this->ting->get(MeetupRepository::class);

            $io->progressStart(count($meetups));
            foreach ($meetups as $meetup) {
                $io->progressAdvance();

                $id = $meetup->getId();
                $existingMeetup = $meetupRepository->get($id);
                if (!$existingMeetup) {
                    $meetupRepository->save($meetup);
                } else {
                    $io->note(sprintf('Meetup id %d déjà en base.', $id));
                }
            }

            $io->progressFinish();
            $io->success('Terminé avec succès');

            return 1;
        } catch (\Exception $e) {
            throw new \Exception('Problème lors du scraping ou de la sauvegarde des évènements Meetup', $e->getCode(), $e);
        }
    }
}
