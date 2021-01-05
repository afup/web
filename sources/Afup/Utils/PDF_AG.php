<?php

namespace Afup\Site\Utils;

use AppBundle\GeneralMeeting\Attendee;

class PDF_AG extends \FPDF
{
    const CELL_HEIGHT = 7;
    private $footerTitle = '';

    /**
     * @param Attendee[] $attendees
     * @param int[]      $validAttendees
     */
    public function prepareContent(array $attendees)
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

        usort($attendees, static function (Attendee $a, Attendee $b) {
            $triA = $a->getLastname().' '.$a->getFirstname();
            if ($a->getPowerLastname()) {
                $triA = $a->getPowerLastname().' '.$a->getPowerFirstname();
            }

            $triB = $b->getLastname().' '.$b->getFirstname();
            if ($b->getPowerLastname()) {
                $triB = $b->getPowerLastname().' '.$b->getPowerFirstname();
            }

            if ($triA !== $triB) {
                return $triA < $triB ? -1 : 1;
            }
            if ($a->getPresence() === $b->getPresence()) {
                return 0;
            }

            return $a->getPresence() < $b->getPresence() ? -1 : 1;
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
                $attendee->getLastname().' '.$attendee->getFirstname(),
                $presence,
                $attendee->getPowerLastname().' '.$attendee->getPowerFirstname(),
                '',
            ]);

            $this->Ln();
        }
    }

    private function writeRow(array $row)
    {
        $widths = [65, 20, 65, 35];

        foreach ($row as $pos => $value) {
            $this->Cell($widths[$pos], self::CELL_HEIGHT, utf8_decode($value), 1);
        }
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode($this->getFooterTitle()).' - Page '.$this->PageNo(), 0, 0, 'C');
    }

    public function getFooterTitle()
    {
        return $this->footerTitle;
    }

    public function setFooterTitle($footerTitle)
    {
        $this->footerTitle = $footerTitle;
    }
}
