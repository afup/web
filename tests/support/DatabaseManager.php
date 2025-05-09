<?php

declare(strict_types=1);

namespace Afup\Tests\Support;

use Ifsnop\Mysqldump\Mysqldump;
use PDO;
use RuntimeException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

/**
 * Helper pour les tests pour recréer une base de données avec un dump.
 */
final readonly class DatabaseManager
{
    private const DB_NAME = 'web';

    private PDO $database;
    private Mysqldump $dumper;
    private string $dbDumpKey;

    public function __construct(private bool $shouldRunSeeds)
    {
        $this->database = new PDO('mysql:host=dbtest;dbname=' . self::DB_NAME, 'root', 'root');
        $this->dumper = new Mysqldump('mysql:host=dbtest;dbname=' . self::DB_NAME, 'root', 'root');
        $this->dbDumpKey = $this->computeDbDumpKey();
    }

    public function reloadDatabase(): void
    {
        $this->resetDb();

        $dbDumpFile = sprintf(__DIR__ . '/../../var/cache/test/db_dump_%s.sql', $this->dbDumpKey);
        if (!is_file($dbDumpFile)) {
            $this->migrateDb();

            if ($this->shouldRunSeeds) {
                $this->seedRun();
            }

            $this->dumper->start($dbDumpFile);
        } else {
            $this->restoreDb($dbDumpFile);
        }
    }

    private function computeDbDumpKey(): string
    {
        $directories = [
            __DIR__ . '/../../db/migrations/',
        ];

        if ($this->shouldRunSeeds) {
            $directories[] = __DIR__ . '/../../db/seeds/';
        }

        $finder = new Finder();
        $files = $finder->files()->in($directories);

        $key = '';
        foreach ($files as $file) {
            $key .= md5_file($file->getRealPath());
        }

        return md5($key);
    }

    private function restoreDb(string $dbDumpFile): void
    {
        if (false === $this->database->exec(file_get_contents($dbDumpFile))) {
            throw new RuntimeException(implode(' ', $this->database->errorInfo()));
        }
    }

    private function resetDb(): void
    {
        $sql = sprintf('DROP DATABASE IF EXISTS %1$s; CREATE DATABASE %1$s; USE %1$s;', self::DB_NAME);
        if (false === $this->database->exec($sql)) {
            throw new RuntimeException(implode(' ', $this->database->errorInfo()));
        }
    }

    private function migrateDb(): void
    {
        $this->runCommand(['./bin/phinx', 'migrate', '-e', 'test']);
    }

    private function seedRun(): void
    {
        $this->runCommand(['./bin/phinx', 'seed:run', '-e', 'test']);
    }

    private function runCommand(array $command): void
    {
        $process = new Process($command);
        $process->mustRun();
    }
}
