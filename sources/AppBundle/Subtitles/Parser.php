<?php

namespace AppBundle\Subtitles;

use Captioning\Format\SubripFile;

class Parser
{
    public function parse($contentSrt)
    {
        if (null === $contentSrt) {
            return [];
        }

        if (0 === strlen(trim($contentSrt))) {
            return [];
        }

        // on passe le quatrième paramètre $_requireStrictFileFormat à false pour autoriser d'avoir des sous-titres qui
        // se chevauchent (sinon on a une excption)
        $subripFile = new SubripFile(null, null, false, false);
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
