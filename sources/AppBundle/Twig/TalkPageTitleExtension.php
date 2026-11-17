<?php

declare(strict_types=1);

namespace AppBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Construit le <title> d'une page talk en restant sous la limite affichée par les moteurs de recherche.
 */
final class TalkPageTitleExtension extends AbstractExtension
{
    public const int MAX_LENGTH = 65;

    private const string SEPARATOR = ' – ';
    private const string ELLIPSIS = '…';

    #[\Override]
    public function getFunctions(): array
    {
        return [
            new TwigFunction('talk_page_title', $this->talkPageTitle(...)),
        ];
    }

    /**
     * « {titre} – {événement} » si l'ensemble tient dans la limite, sinon le titre seul,
     * tronqué sur une frontière de mot avec « … » s'il la dépasse encore.
     */
    public function talkPageTitle(string $talkTitle, string $eventTitle): string
    {
        $talkTitle = trim($talkTitle);
        $eventTitle = trim($eventTitle);

        if ($eventTitle !== '') {
            $withEvent = $talkTitle . self::SEPARATOR . $eventTitle;
            if (mb_strlen($withEvent) <= self::MAX_LENGTH) {
                return $withEvent;
            }
        }

        if (mb_strlen($talkTitle) <= self::MAX_LENGTH) {
            return $talkTitle;
        }

        $available = self::MAX_LENGTH - mb_strlen(self::ELLIPSIS);
        $truncated = mb_substr($talkTitle, 0, $available);

        // Le mot est coupé si le caractère suivant la coupe n'est pas un espace : on recule au dernier espace.
        if (!preg_match('/^\s/u', mb_substr($talkTitle, $available, 1))) {
            $lastSpace = mb_strrpos($truncated, ' ');
            if ($lastSpace !== false && $lastSpace > 0) {
                $truncated = mb_substr($truncated, 0, $lastSpace);
            }
        }

        return rtrim($truncated, " \t\n\r\0\x0B,;:-–—") . self::ELLIPSIS;
    }
}
