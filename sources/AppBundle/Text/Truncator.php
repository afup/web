<?php

declare(strict_types=1);

namespace AppBundle\Text;

/**
 * Tronque un texte sur une frontière de mot, avec « … », sans dépasser une longueur en caractères.
 * Commun au <title> des talks et aux meta descriptions.
 */
final class Truncator
{
    private const string ELLIPSIS = '…';

    public static function truncate(string $text, int $maxLength): string
    {
        if (mb_strlen($text) <= $maxLength) {
            return $text;
        }

        // Un caractère de plus que la place disponible : s'il s'agit d'un espace, le dernier mot est entier.
        $truncated = mb_substr($text, 0, $maxLength - mb_strlen(self::ELLIPSIS) + 1);
        $lastSpace = mb_strrpos($truncated, ' ');
        $truncated = false !== $lastSpace && $lastSpace > 0
            ? mb_substr($truncated, 0, $lastSpace)
            : mb_substr($truncated, 0, -1);

        // Pas d'espace ni de ponctuation devant « … ». En mode /u : rtrim() couperait un caractère multi-octet.
        return (string) preg_replace('/[\s,;:.\-–—]+$/u', '', $truncated) . self::ELLIPSIS;
    }
}
