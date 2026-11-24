<?php

declare(strict_types=1);

namespace AppBundle\Twig;

use AppBundle\Text\Truncator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Construit le <title> d'une page talk en restant sous la limite affichée par les moteurs de recherche.
 */
final class TalkPageTitleExtension extends AbstractExtension
{
    public const int MAX_LENGTH = 65;

    private const string SEPARATOR = ' – ';

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

        return Truncator::truncate($talkTitle, self::MAX_LENGTH);
    }
}
