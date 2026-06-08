<?php

declare(strict_types=1);

namespace AppBundle\Twig;

use AppBundle\SocialNetwork\Bluesky\BlueskyOembedClient;
use Parsedown;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(
        private readonly Parsedown $parsedown,
        private readonly Parsedown $emailParsedown,
        private readonly BlueskyOembedClient $blueskyOembedClient,
        #[Autowire(env: 'bool:GOOGLE_ANALYTICS_ENABLED')]
        private readonly bool $googleAnalyticsEnabled,
        #[Autowire(env: 'GOOGLE_ANALYTICS_ID')]
        private readonly string $googleAnalyticsId,
    ) {}

    #[\Override]
    public function getFunctions(): array
    {
        return [
            new TwigFunction('render_curl', function ($url) {
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);

                if (curl_getinfo($ch, CURLINFO_RESPONSE_CODE) === 200) {
                    return $response;
                }
                return '';
            }, ['is_safe' => ['html']]),
            new TwigFunction('bluesky_oembed', function (string $url): string {
                $html = $this->blueskyOembedClient->getEmbedHtml($url);
                return $html ?: '<a href="' . htmlspecialchars($url) . '">' . htmlspecialchars($url) . '</a>';
            }, ['is_safe' => ['html']]),
        ];
    }

    #[\Override]
    public function getFilters(): array
    {
        return [
            new TwigFilter('markdown', fn($text) => $this->parsedown->text($text), ['is_safe' => ['html']]),
            new TwigFilter('markdown_email', fn($text) => $this->emailParsedown->text($text), ['is_safe' => ['html']]),
        ];
    }

    public function getGlobals(): array
    {
        return [
            'google_analytics_enabled' => $this->googleAnalyticsEnabled,
            'google_analytics_id' => $this->googleAnalyticsId,
        ];
    }

    public function getName(): string
    {
        return 'app';
    }
}
