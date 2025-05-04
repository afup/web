<?php

declare(strict_types=1);


namespace AppBundle\CFP;

use Afup\Site\Utils\Utils;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Speaker;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PhotoStorage
{
    private readonly Filesystem $filesystem;

    const DIR_ORIGINAL = 'originals';
    const DIR_THUMBS = 'thumbnails';

    const FORMAT = [
        'originals' => ['width' => 1000, 'height' => 1000],
        'thumbnails' => ['width' => 90, 'height' => 120],
    ];

    public function __construct(
        private $basePath,
        private $publicPath,
        private $legacyBasePath,
    ) {
        $this->filesystem = new Filesystem();
    }

    public function store(File $photo, Speaker $speaker): string
    {
        $fileName = $speaker->getId() . '.' . $photo->guessExtension();
        $directory = $this->basePath . '/' . $speaker->getEventId() . '/' . self::DIR_ORIGINAL;
        $this->createDirectory($directory);
        // delete all formats
        if ($speaker->getId() !== null) {
            foreach (array_keys(self::FORMAT) as $format) {
                $files = glob($this->basePath . '/' . $speaker->getEventId() . '/' . $format . '/' . $speaker->getId() . '.*');
                $this->filesystem->remove($files);
            }
        }

        $photo->move($directory, $fileName);

        return $fileName;
    }

    public function getUrl(Speaker $speaker, $format = null): ?string
    {
        if ($format === null) {
            $format = self::DIR_THUMBS;
        }
        if (!in_array($format, [self::DIR_ORIGINAL, self::DIR_THUMBS])) {
            throw new \UnexpectedValueException(sprintf('Bad format: %s', $format));
        }
        // We have to check if the file exists or create it from the original size
        if ($format !== self::DIR_ORIGINAL && !$this->filesystem->exists($this->getPath($speaker, $format))) {
            $this->generateFormat($speaker, $format);
        }

        if ($this->filesystem->exists($this->getPath($speaker, $format))) {
            return $this->publicPath . '/' . $speaker->getEventId() . '/' . $format . '/' . $speaker->getPhoto();
        }

        return null;
    }

    public function getPath(Speaker $speaker, string $format): string
    {
        if (!in_array($format, [self::DIR_ORIGINAL, self::DIR_THUMBS])) {
            throw new \UnexpectedValueException(sprintf('Bad format: %s', $format));
        }

        $directory = $this->basePath . '/' . $speaker->getEventId() . '/' . $format;
        $this->createDirectory($directory);

        return $directory . '/' . $speaker->getPhoto();
    }

    public function storeLegacy(UploadedFile $photo, Event $event, Speaker $speaker): string
    {
        $dir = '/templates/' . $event->getPath() . '/images/intervenants';
        $path = $dir . '/' . $speaker->getId() . '.jpg';
        // Transformation en 90x120 JPG pour simplifier
        if ($photo->getMimeType() === 'image/png') {
            $img = imagecreatefrompng($photo->getRealPath());
        } else {
            $img = imagecreatefromjpeg($photo->getRealPath());
        }
        $width = imagesx($img);
        $height = imagesy($img);
        if ($width !== 90 || $height !== 120) {
            $oldImg = $img;
            $img = imagecreatetruecolor(90, 120);
            imagecopyresampled($img, $oldImg, 0, 0, 0, 0, 90, 120, $width, $height);
        }
        $this->createDirectory($this->legacyBasePath . $dir);
        imagejpeg($img, $this->legacyBasePath . $path, 90);

        return $path;
    }

    public function getLegacyUrl(Event $event, Speaker $speaker): ?string
    {
        $path = '/templates/' . $event->getPath() . '/images/intervenants/' . $speaker->getId() . '.jpg';
        if (is_file($this->legacyBasePath . $path)) {
            return $path;
        }

        return null;
    }

    public function storeFromGravatar(Speaker $speaker): ?string
    {
        $tmpImagePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('gravatar-', true) . '.jpg';
        // Transformation en 90x120 JPG pour simplifier
        $url = Utils::get_gravatar($speaker->getEmail(), 90);
        $img = @imagecreatefromjpeg($url);
        if (!is_resource($img)) {
            $img = imagecreatefrompng($url);
        }
        if (imagejpeg($img, $tmpImagePath, 90)) {
            return $this->store(new File($tmpImagePath), $speaker);
        }

        return null;
    }

    private function generateFormat(Speaker $speaker, string $format): void
    {
        $originalPath = $this->getPath($speaker, self::DIR_ORIGINAL);
        $formatPath = $this->getPath($speaker, $format);

        $size = self::FORMAT[$format];

        if (file_exists($originalPath) === false) {
            return ;
        }
        $ext = substr((string) $speaker->getPhoto(), -4);
        $transparent = false;
        // This part is just our old script. We should do better
        if ($ext === '.png') {
            $transparent = true;
            $img = imagecreatefrompng($originalPath);
        } else {
            $img = imagecreatefromjpeg($originalPath);
        }
        $originalWidth = $width = imagesx($img);
        $originalHeight = $height = imagesy($img);
        if ($width > $size['width'] || $height > $size['height']) {
            $oldImg = $img;

            $ratio = $width / $height;

            /**
             *  RATIO = WIDTH / HEIGHT
             *
             *  RATIO / WIDTH = 1 / HEIGHT ==> WIDTH / RATIO = HEIGHT
             *  RATIO x HEIGHT = WIDTH
             *
             * **************************
             * HEIGHT = WIDTH / RATIO
             * WIDTH = RATIO x HEIGHT
             * **************************
             */


            if ($width > $size['width']) {
                $width = $size['width'];
                $height = (int) ($width / $ratio);
            }
            // If after that, height is still to high
            if ($height > $size['height']) {
                $height = $size['height'];
                $width = (int) ($ratio * $height);
            }

            $img = imagecreatetruecolor($width, $height);
            if ($transparent) {
                imagecolortransparent($img, imagecolorallocatealpha($img, 0, 0, 0, 127));
                imagealphablending($img, false);
                imagesavealpha($img, true);
            }
            imagecopyresampled($img, $oldImg, 0, 0, 0, 0, $width, $height, $originalWidth, $originalHeight);
        }
        if ($ext === '.png') {
            imagepng($img, $formatPath, 9);
        } else {
            imagejpeg($img, $formatPath, 98);
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
}
