<?php

declare(strict_types=1);

namespace Afup\Site\Utils;

use Afup\Site\Comptabilite\PDF;
use AppBundle\GeneralMeeting\Attendee;

class PDF_AG extends PDF
{
    const CELL_HEIGHT = 7;
    private $footerTitle = '';

    /**
     * @param Attendee[] $attendees
     */
    public function prepareContent(array $attendees): void
    {
        $this->AddPage();

        $this->SetFont('Arial', 'B', 11);

        $this->writeRow([
            'Nom / Prénom',
            'Présence',
            'Pouvoir',
            'Émargement',
        ]);

        $this->Ln();

        usort($attendees, static function (Attendee $a, Attendee $b): int {
            $triA = $a->getLastname() . ' ' . $a->getFirstname();
            if ($a->getPowerLastname()) {
                $triA = $a->getPowerLastname() . ' ' . $a->getPowerFirstname();
            }

            $triB = $b->getLastname() . ' ' . $b->getFirstname();
            if ($b->getPowerLastname()) {
                $triB = $b->getPowerLastname() . ' ' . $b->getPowerFirstname();
            }

            if ($triA !== $triB) {
                return $triA < $triB ? -1 : 1;
            }
            return $a->getPresence() <=> $b->getPresence();
        });

        foreach ($attendees as $attendee) {
            $this->SetFont('Arial', '', 12);

            $presence = '??';
            if ($attendee->isPresent()) {
                $presence = 'Présent';
            } elseif ($attendee->isAbsent()) {
                $presence = 'Absent';
            }

            $this->writeRow([
                $attendee->getLastname() . ' ' . $attendee->getFirstname(),
                $presence,
                $attendee->getPowerLastname() . ' ' . $attendee->getPowerFirstname(),
                '',
            ]);

            $this->Ln();
        }
    }

    private function writeRow(array $row): void
    {
        $widths = [65, 20, 65, 35];

        foreach ($row as $pos => $value) {
            $this->Cell($widths[$pos], self::CELL_HEIGHT, $value, 1);
        }
    }

    public function Footer(): void
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, $this->getFooterTitle() . ' - Page ' . $this->PageNo(), 0, 0, 'C');
    }

    public function getFooterTitle()
    {
        return $this->footerTitle;
    }

    public function setFooterTitle($footerTitle): void
    {
        $this->footerTitle = $footerTitle;
    }
}
