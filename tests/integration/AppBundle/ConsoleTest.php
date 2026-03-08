<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests;

use AppBundle\Tests\TestCase;
use Symfony\Component\Process\Process;

final class ConsoleTest extends TestCase
{
    public function testConsoleRunsWithoutError(): void
    {
        $process = new Process(['php', 'bin/console']);
        $process->run();

        $this->assertSame(
            0,
            $process->getExitCode(),
            sprintf(
                "The console exited with a non-zero code. Output:\n%s\nError Output:\n%s",
                $process->getOutput(),
                $process->getErrorOutput(),
            ),
        );
    }
}
