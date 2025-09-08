<?php

declare(strict_types=1);

namespace AppBundle\GeneralMeeting;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Finder\Finder;

final readonly class ReportListBuilder
{
    public function __construct(
        #[Autowire('%app.general_meetings_dir%')]
        private string $storageDir,
    ) {}

    /**
     * @return array{date: (string | false), label: (string | false), filename: mixed, path: mixed}[]
     */
    public function prepareGeneralMeetingsReportsList(): array
    {
        if (!is_dir($this->storageDir)) {
            return [];
        }

        $files = (new Finder())->name("*.pdf")->in($this->storageDir);

        $reports = [];
        foreach ($files as $file) {
            $reports[$file->getFilename()] = [
                'date' => substr($file->getFilename(), 0, 10),
                'label' => substr($file->getFilename(), 11, -4),
                'filename' => $file->getFilename(),
                'path' => $file->getRealPath(),
            ];
        }

        krsort($reports);

        return $reports;
    }
}
