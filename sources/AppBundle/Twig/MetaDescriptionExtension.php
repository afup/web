<?php

declare(strict_types=1);

namespace AppBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Transforme un contenu (HTML, texte avec entités) en texte brut utilisable
 * dans une balise <meta name="description"> : balises retirées, entités décodées,
 * espaces normalisés, troncature sur une frontière de mot.
 */
final class MetaDescriptionExtension extends AbstractExtension
{
    public const int MAX_LENGTH = 155;

    private const string ELLIPSIS = '…';

    #[\Override]
    public function getFilters(): array
    {
        return [
            new TwigFilter('meta_description', $this->metaDescription(...)),
        ];
    }

    public function metaDescription(string|\Stringable|null $content, int $maxLength = self::MAX_LENGTH): string
    {
        $content = (string) $content;

        // Sans ce séparateur, « <p>Un</p><p>Deux</p> » donnerait « UnDeux ».
        $content = preg_replace('#<(br|/p|/div|/li|/h[1-6]|/blockquote|/pre|/tr|/td|/th)\b[^>]*>#i', '$0 ', $content);
        $text = html_entity_decode(strip_tags((string) $content), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = trim((string) preg_replace('/\s+/u', ' ', $text));

        if (mb_strlen($text) <= $maxLength) {
            return $text;
        }

        $truncated = mb_substr($text, 0, $maxLength - mb_strlen(self::ELLIPSIS) + 1);
        $lastSpace = mb_strrpos($truncated, ' ');
        if ($lastSpace !== false) {
            $truncated = mb_substr($truncated, 0, $lastSpace);
        } else {
            $truncated = mb_substr($truncated, 0, -1);
        }

        return rtrim($truncated, ' ,;:.-–—') . self::ELLIPSIS;
    }
}
