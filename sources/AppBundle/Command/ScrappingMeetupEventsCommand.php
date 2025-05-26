<?php

declare(strict_types=1);

namespace AppBundle\Command;

use AppBundle\Event\Model\Repository\MeetupRepository;
use AppBundle\Indexation\Meetups\MeetupClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ScrappingMeetupEventsCommand extends Command
{
    use LockableTrait;

    public function __construct(
        private readonly MeetupClient $meetupClient,
        private readonly MeetupRepository $meetupRepository,
    ) {
        parent::__construct();
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

            return Command::SUCCESS;
        }

        try {
            $meetups = $this->meetupClient->getEvents();

            $io->progressStart(count($meetups));
            foreach ($meetups as $meetup) {
                $io->progressAdvance();

                $id = $meetup->getId();
                $existingMeetup = $this->meetupRepository->get($id);

                // Si le meetup est déjà présent en base, il est mis à jour.
                if ($existingMeetup) {
                    $existingMeetup->setTitle($meetup->getTitle());
                    $existingMeetup->setDescription($meetup->getDescription());
                    $existingMeetup->setLocation($meetup->getLocation());
                    $existingMeetup->setDate($meetup->getDate());

                    // On doit remplacer la variable, car l'ORM a une référence vers l'instance récupérée
                    // via le get et pas celle de la boucle.
                    $meetup = $existingMeetup;
                }

                $this->meetupRepository->save($meetup);
            }

            $io->progressFinish();
            $io->success('Terminé avec succès');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            throw new \Exception('Problème lors du scraping ou de la sauvegarde des évènements Meetup', $e->getCode(), $e);
        }

        return Command::FAILURE;
    }
}
