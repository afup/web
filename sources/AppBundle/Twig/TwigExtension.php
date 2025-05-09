<?php

declare(strict_types=1);

namespace AppBundle\Twig;

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
        #[Autowire('%google_analytics_enabled%')]
        private readonly string $googleAnalyticsEnabled,
        #[Autowire('%google_analytics_id%')]
        private readonly string $googleAnalyticsId,
    ) {
    }

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
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('markdown', fn ($text) => $this->parsedown->text($text), ['is_safe' => ['html']]),
            new TwigFilter('markdown_email', fn ($text) => $this->emailParsedown->text($text), ['is_safe' => ['html']]),
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
