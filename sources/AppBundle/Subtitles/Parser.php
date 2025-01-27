<?php

declare(strict_types=1);

namespace AppBundle\Subtitles;

use Captioning\Format\SubripCue;
use Captioning\Format\SubripFile;

class Parser
{
    /**
     * @return array{start: (float | false | null), text: mixed}[]
     */
    public function parse($contentSrt): array
    {
        if (null === $contentSrt) {
            return [];
        }

        if (trim($contentSrt) === '') {
            return [];
        }

        // on passe le quatrième paramètre $_requireStrictFileFormat à false pour autoriser d'avoir des sous-titres qui
        // se chevauchent (sinon on a une excption)
        $subripFile = new SubripFile(null, null, false, false);
        $subripFile->loadFromString($contentSrt);

        $parsedContent = [];

        $currentText = null;
        $currentStart = null;

        /** @var SubripCue $cue */
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
