<?php


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
    private $basePath;
    private $publicPath;
    private $filesystem;
    /** @var EventRepository */
    private $eventRepository;
    /** @var LoggerInterface */
    private $logger;

    public function __construct($basePath, $publicPath, $eventRepository)
    {
        $this->basePath = $basePath;
        $this->publicPath = $publicPath;
        $this->filesystem = new Filesystem();
        $this->eventRepository = $eventRepository;
    }

    public function store(UploadedFile $file, Speaker $speaker)
    {
        $fileName = $this->buildFilename($file->getClientOriginalName(), $speaker);
        $directory = $this->getDir($speaker);

        $this->createDirectory($directory);
        $file->move($directory, $fileName);

        return $fileName;
    }

    public function delete($filename, Speaker $speaker)
    {
        $file = $this->buildFilename($filename, $speaker);
        $dirname = $this->getDir($speaker);
        $path = $dirname . '/' . $file;
        if ($this->filesystem->exists($path)) {
            $this->filesystem->remove($path);
        }
    }

    public function getFiles(Speaker $speaker)
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

    /**
     * @param LoggerInterface $logger
     *
     * @return $this
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    public function cleanFiles($duration = 'P12M')
    {
        $beforeDate = new \DateTime();
        $beforeDate->sub(new \DateInterval($duration));

        $this->logInfo(sprintf('Speakers Expenses Storages clean before "%s"', $beforeDate->format('Y-m-d')));

        $events = $this->eventRepository->getPreviousEventsBefore($beforeDate);

        /** @var Event $event */
        foreach ($events as $event) {
            $this->logInfo(sprintf('Event "%s" #%d [%s]: ', $event->getTitle(), $event->getId(), $event->getDateStart()->format('Y-m-d')));

            $directory = $this->basePath . '/' . $event->getId();

            if ($this->filesystem->exists($directory)) {
                $this->filesystem->remove($directory);
                $this->logInfo(sprintf('Removing "%s" directory OK', $directory));
            } else {
                $this->logInfo(sprintf('Directory "%s" does not exists', $directory));
            }
        }
    }

    private function logInfo($message)
    {
        if ($this->logger) {
            $this->logger->info($message);
        }
    }

    private function createDirectory($directory)
    {
        try {
            $this->filesystem->mkdir($directory, 0755);
        } catch (IOException $exception) {
            throw new FileException('Could not create directory for storage', 0, $exception);
        }
    }

    private function getDir(Speaker $speaker)
    {
        return $this->basePath . '/' . $speaker->getEventId();
    }

    private function getPath(Speaker $speaker)
    {
        return $this->publicPath . '/' . $speaker->getEventId() . '/';
    }

    private function buildFilename($file, $speaker)
    {
        return sprintf('%d_%s', $speaker->getId(), $file);
    }
}
