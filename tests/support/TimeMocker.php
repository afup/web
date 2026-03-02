<?php

declare(strict_types=1);

namespace Afup\Tests\Support;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

final readonly class TimeMocker
{
    private const MOCK_DIR_PATH = __DIR__ . '/../../var/cache/test/afup/';
    private const MOCK_FILE_PATH = self::MOCK_DIR_PATH . 'current-date-mock';

    private Filesystem $filesystem;

    public function __construct()
    {
        $this->filesystem = new Filesystem();
    }

    public function setCurrentDateMock(string $dateString): void
    {
        $this->filesystem->mkdir(self::MOCK_DIR_PATH);
        $this->filesystem->dumpFile(self::MOCK_FILE_PATH, $dateString);
    }

    public function clearCurrentDateMock(): void
    {
        try {
            $this->filesystem->remove(self::MOCK_FILE_PATH);
        } catch (IOException) {
            // Il y a une exception aussi si le fichier n'existe pas
            // mais ce n'est pas important à propager.
        }
    }

    public function getCurrentDateMock(): ?\DateTimeImmutable
    {
        try {
            return new \DateTimeImmutable($this->filesystem->readFile(self::MOCK_FILE_PATH));
        } catch (IOException) {
            return null;
        }
    }
}
