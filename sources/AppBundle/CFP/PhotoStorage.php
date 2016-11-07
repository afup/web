<?php


namespace AppBundle\CFP;


use AppBundle\Event\Model\Speaker;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PhotoStorage
{
    private $basePath;
    private $publicPath;

    const DIR_ORIGINAL = 'originals';
    const DIR_THUMBS = 'thumbnails';

    const FORMAT = [
        'originals' => ['width' => 1000, 'height' => 1000],
        'thumbnails' => ['width' => 90, 'height' => 120]
    ];

    public function __construct($basePath, $publicPath)
    {
        $this->basePath = $basePath;
        $this->publicPath = $publicPath;
    }

    public function store(UploadedFile $photo, Speaker $speaker)
    {
        $fileName = $speaker->getId() . '.' . $photo->guessExtension();

        $directory = $this->basePath . '/' . $speaker->getEventId() . '/' . self::DIR_ORIGINAL;
        if (file_exists($directory) === false) {
            mkdir($directory, 0755, true);
        } elseif(is_dir($directory) === false || is_writable($directory) === false) {
            throw new FileException(sprintf('Could not create directory for storage'));
        }

        // delete all formats
        if ($speaker->getId() === null) {
            foreach (self::FORMAT as $format => $sizes) {
                $files = glob($this->basePath . '/' . $speaker->getEventId() . '/' . $format . '/' . $speaker->getId() . '.*');
                foreach ($files as $file) {
                    unlink($file);
                }
            }
        }

        $photo->move(
            $directory,
            $fileName
        );

        return $fileName;
    }

    public function getUrl(Speaker $speaker, $format = null)
    {
        if ($format === null) {
            $format = self::DIR_THUMBS;
        }
        if (!in_array($format, [self::DIR_ORIGINAL, self::DIR_THUMBS])) {
            throw new \UnexpectedValueException(sprintf('Bad format: %s', $format));
        }
        if ($format !== self::DIR_ORIGINAL) {
            // We have to check if the file exists or create it from the original size
            if (file_exists($this->getPath($speaker, $format)) === false) {
                $this->generateFormat($speaker, $format);
            }
        }

        return $this->publicPath . '/' . $speaker->getEventId() . '/' . $format . '/' . $speaker->getPhoto();
    }

    public function getPath(Speaker $speaker, $format)
    {
        if (!in_array($format, [self::DIR_ORIGINAL, self::DIR_THUMBS])) {
            throw new \UnexpectedValueException(sprintf('Bad format: %s', $format));
        }

        $directory = $this->basePath . '/' . $speaker->getEventId() . '/' . $format;
        if (file_exists($directory) === false) {
            mkdir($directory, 0755, true);
        } elseif(is_dir($directory) === false || is_writable($directory) === false) {
            throw new FileException(sprintf('Could not create directory for storage'));
        }

        return $directory . '/' . $speaker->getPhoto();
    }

    private function generateFormat(Speaker $speaker, $format)
    {
        $originalPath = $this->getPath($speaker, self::DIR_ORIGINAL);
        $formatPath = $this->getPath($speaker, $format);

        $size = self::FORMAT[$format];

        $ext = substr($speaker->getPhoto(), -4);
        // This part is just our old script. We should do better
        if ($ext === '.png') {
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
            imagecopyresampled($img, $oldImg, 0, 0, 0, 0, $width, $height, $originalWidth, $originalHeight);
        }
        imagejpeg($img, $formatPath, 98);
    }
}
