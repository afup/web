<?php

declare(strict_types=1);

namespace AppBundle\Markdown;

use League\CommonMark\Normalizer\TextNormalizerInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

final readonly class SymfonySlugNormalizer implements TextNormalizerInterface
{
    public function normalize(string $text, array $context = []): string
    {
        return (new AsciiSlugger())->slug($text)->lower()->toString();
    }
}
