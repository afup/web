<?php

namespace AppBundle\Subtitles;

use Captioning\Format\SubripFile;

class Parser
{
    public function parse($contentSrt)
    {
        $subripFile = new SubripFile();
        $subripFile->loadFromString($contentSrt);

        $parsedContent = [];

        $currentText = null;
        $currentStart = null;

        /** @var \Captioning\Format\SubripCue $cue */
        foreach ($subripFile->getCues() as $cue) {

            if (null === $currentText) {
                $currentText = $cue->getText();
                $currentStart = round($cue->getStartMS() / 1000);
            } else {
                $currentText .= ' ' . $cue->getText();
            }

            if (substr($currentText, -1) == '.') {
                $parsedContent[] = [
                    'start' => $currentStart,
                    'text' => $currentText,
                ];

                $currentText = null;
                $currentStart = null;
            }
        }

        if (null !== $currentText) {
            $parsedContent[] = [
                'start' => $currentStart,
                'text' => $currentText,
            ];
        }

        return $parsedContent;
    }
}
