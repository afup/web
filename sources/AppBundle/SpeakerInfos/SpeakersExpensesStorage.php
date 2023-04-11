<?php

declare(strict_types=1);


namespace AppBundle\SpeakerInfos;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Speaker;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class SpeakersExpensesStorage
{
    private string $basePath;
    private Filesystem $filesystem;
    private EventRepository $eventRepository;

    public function __construct(string $basePath, EventRepository $eventRepository)
    {
        $this->basePath = $basePath;
        $this->filesystem = new Filesystem();
        $this->eventRepository = $eventRepository;
    }

    public function store(UploadedFile $file, Speaker $speaker): string
    {
        $fileName = $this->buildFilename($file->getClientOriginalName(), $speaker);
        $directory = $this->getDir($speaker);

        $this->createDirectory($directory);
        $file->move($directory, $fileName);

        return $fileName;
    }

    public function delete($filename, Speaker $speaker): void
    {
        $file = $this->buildFilename($filename, $speaker);
        $dirname = $this->getDir($speaker);
        $path = $dirname . '/' . $file;
        if ($this->filesystem->exists($path)) {
            $this->filesystem->remove($path);
        }
    }

    /**
     * @return array{basename: (array | string), path: non-falsy-string}[]
     */
    public function getFiles(Speaker $speaker): array
    {
        $directory = $this->getDir($speaker);
        $this->createDirectory($directory);

        $finder = new Finder();
        $iterator = $finder->files()->name(sprintf('%d_*', $speaker->getId()))->in($directory);

        $files = [];
        foreach ($iterator as $file) {
            $basename = str_replace(sprintf('%d_', $speaker->getId()), '', $file->getBasename());
            $files[] = [
                'basename' => $basename,
                'path' => $directory . '/' . $file->getBasename()
            ];
        }
        return $files;
    }

    public function cleanFiles(LoggerInterface $logger, $duration = 'P12M'): void
    {
        $beforeDate = new \DateTime();
        $beforeDate->sub(new \DateInterval($duration));

        $logger->info(sprintf('Speakers Expenses Storages clean before "%s"', $beforeDate->format('Y-m-d')));

        $events = $this->eventRepository->getPreviousEventsBefore($beforeDate);

        /** @var Event $event */
        foreach ($events as $event) {
            $logger->info(sprintf('Event "%s" #%d [%s]: ', $event->getTitle(), $event->getId(), $event->getDateStart()->format('Y-m-d')));

            $directory = $this->basePath . '/' . $event->getId();

            if ($this->filesystem->exists($directory)) {
                $this->filesystem->remove($directory);
                $logger->info(sprintf('Removing "%s" directory OK', $directory));
            } else {
                $logger->info(sprintf('Directory "%s" does not exists', $directory));
            }
        }
    }

    private function createDirectory(string $directory): void
    {
        try {
            $this->filesystem->mkdir($directory, 0755);
        } catch (IOException $exception) {
            throw new FileException('Could not create directory for storage', 0, $exception);
        }
    }

    private function getDir(Speaker $speaker): string
    {
        return $this->basePath . '/' . $speaker->getEventId();
    }

    private function buildFilename($file, Speaker $speaker): string
    {
        return sprintf('%d_%s', $speaker->getId(), $file);
    }
}
