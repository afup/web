<?php

declare(strict_types=1);

namespace AppBundle\Twig;

use AppBundle\Site\Entity\Article;
use Symfony\Component\String\TruncateMode;
use Twig\Extension\AbstractExtension;
use Twig\Extra\Markdown\MarkdownInterface;
use Twig\TwigFilter;

use function Symfony\Component\String\u;

/**
 * Aperçu en texte brut d'une actualité (liste des news, réseaux sociaux) :
 * le chapeau, ou à défaut le début du contenu, sans Markdown ni balises.
 * Le résultat n'est pas du HTML : il doit être échappé par Twig.
 */
final class NewsResumeExtension extends AbstractExtension
{
    public const int CONTENT_LENGTH = 200;

    public function __construct(private readonly MarkdownInterface $markdown) {}

    #[\Override]
    public function getFilters(): array
    {
        return [
            new TwigFilter('news_resume', $this->resume(...)),
        ];
    }

    public function resume(Article $article): string
    {
        if ((string) $article->chapeau !== '') {
            return $this->toText((string) $article->chapeau);
        }

        return u($this->toText((string) $article->contenu))
            ->truncate(self::CONTENT_LENGTH, '…', TruncateMode::WordAfter)
            ->toString();
    }

    private function toText(string $markdown): string
    {
        $html = $this->markdown->convert($markdown);
        // Sans espace, « Un<br>Deux » donnerait « UnDeux ».
        $html = (string) preg_replace('#<br\s*/?>#i', ' ', $html);
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim((string) preg_replace('/\s+/u', ' ', $text));
    }
}
