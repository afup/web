<?php

declare(strict_types=1);

namespace AppBundle\Markdown;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkProcessor;
use League\CommonMark\MarkdownConverter;
use Tempest\Highlight\CommonMark\HighlightExtension;
use Twig\Extra\Markdown\MarkdownInterface;

final readonly class Markdown implements MarkdownInterface
{
    private MarkdownConverter $converter;

    public function __construct()
    {
        $environment = new Environment([
            'heading_permalink' => [
                'symbol' => '',
                'apply_id_to_heading' => true,
                'insert' => HeadingPermalinkProcessor::INSERT_NONE,
            ],
            'slug_normalizer' => [
                'instance' => new SymfonySlugNormalizer(),
            ],
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new AutolinkExtension());
        $environment->addExtension(new HeadingPermalinkExtension());
        $environment->addExtension(new HighlightExtension());

        $this->converter = new MarkdownConverter($environment);
    }

    public function convert(string $body): string
    {
        return $this->converter->convert($body)->getContent();
    }
}
