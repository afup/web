<?php

declare(strict_types=1);


namespace AppBundle\Twig;

use AppBundle\Routing\LegacyRouter;
use Psr\Container\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension implements GlobalsInterface
{
    private LegacyRouter $legacyRouter;
    private \Parsedown $parsedown;
    private \Parsedown $emailParsedown;
    private ContainerInterface $container;

    public function __construct(LegacyRouter $legacyRouter, \Parsedown $parsedown, \Parsedown $emailParsedown, ContainerInterface $container)
    {
        $this->legacyRouter = $legacyRouter;
        $this->parsedown = $parsedown;
        $this->emailParsedown = $emailParsedown;
        $this->container = $container;
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
            }, ['is_safe' => ['html']])
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
            'legacy_router' => $this->legacyRouter,
            'google_analytics_enabled' => $this->container->getParameter('google_analytics_enabled'),
            'google_analytics_id' => $this->container->getParameter('google_analytics_id')
        ];
    }

    public function getName(): string
    {
        return 'app';
    }
}
