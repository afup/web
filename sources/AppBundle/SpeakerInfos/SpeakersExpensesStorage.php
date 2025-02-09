<?php

declare(strict_types=1);


namespace AppBundle\SpeakerInfos;

use AppBundle\Event\Model\Speaker;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class SpeakersExpensesStorage
{
    private $basePath;
    private Filesystem $filesystem;

    public function __construct($basePath)
    {
        $this->basePath = $basePath;
        $this->filesystem = new Filesystem();
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
