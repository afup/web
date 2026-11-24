<?php

declare(strict_types=1);

namespace AppBundle\Tests\CFP;

use AppBundle\CFP\PhotoStorage;
use AppBundle\Event\Entity\Speaker;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

final class PhotoStorageTest extends TestCase
{
    private string $basePath;

    protected function setUp(): void
    {
        $this->basePath = sys_get_temp_dir() . '/' . uniqid('photo-storage-', true);
    }

    protected function tearDown(): void
    {
        new Filesystem()->remove($this->basePath);
    }

    public function testCopyFromCopiesTheOriginalPhotoIntoTheFolderOfTheTargetEvent(): void
    {
        $filesystem = new Filesystem();
        $filesystem->dumpFile($this->basePath . '/3/originals/4.png', 'photo');
        // Ancienne vignette de la cible : elle doit être régénérée depuis la nouvelle photo
        $filesystem->dumpFile($this->basePath . '/1/thumbnails/12.jpg', 'old');
        $storage = new PhotoStorage($this->basePath, $this->basePath);

        $fileName = $storage->copyFrom($this->speaker(4, 3, '4.png'), $this->speaker(12, 1, null));

        self::assertSame('12.png', $fileName);
        self::assertSame('photo', file_get_contents($this->basePath . '/1/originals/12.png'));
        self::assertFileExists($this->basePath . '/3/originals/4.png', 'la source est conservée');
        self::assertFileDoesNotExist($this->basePath . '/1/thumbnails/12.jpg');
    }

    public function testCopyFromReturnsNullWhenTheSourceHasNoPhotoFile(): void
    {
        $storage = new PhotoStorage($this->basePath, $this->basePath);

        self::assertNull($storage->copyFrom($this->speaker(4, 3, '4.png'), $this->speaker(12, 1, null)));
        self::assertNull($storage->copyFrom($this->speaker(4, 3, null), $this->speaker(12, 1, null)));
    }

    private function speaker(int $id, int $eventId, ?string $photo): Speaker
    {
        $speaker = new Speaker();
        $speaker->id = $id;
        $speaker->eventId = $eventId;
        $speaker->photo = $photo;

        return $speaker;
    }
}
