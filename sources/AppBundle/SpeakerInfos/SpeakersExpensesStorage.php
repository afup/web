<?php


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
    private $publicPath;
    private $filesystem;

    public function __construct($basePath, $publicPath)
    {
        $this->basePath = $basePath;
        $this->publicPath = $publicPath;
        $this->filesystem = new Filesystem();
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
        $path = $this->getPath($speaker);
        $directory = $this->getDir($speaker);
        $this->createDirectory($directory);

        $finder = new Finder();
        $iterator = $finder->files()->name(sprintf('%d_*', $speaker->getId()))->in($directory);

        $files = [];
        foreach ($iterator as $file) {
            $basename = str_replace(sprintf('%d_', $speaker->getId()), '', $file->getBasename());
            $files[] = [
                'basename' => $basename,
                'path' => $path . $file->getBasename()
            ];
        }
        return $files;
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
