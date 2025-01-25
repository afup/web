<?php


namespace AppBundle\Twig;

use AppBundle\Routing\LegacyRouter;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Container;

class TwigExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    /**
     * @var LegacyRouter
     */
    private $legacyRouter;

    /**
     * @var \Parsedown
     */
    private $parsedown;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var \Parsedown
     */
    private $emailParsedown;

    public function __construct(LegacyRouter $legacyRouter, \Parsedown $parsedown, \Parsedown $emailParsedown, ContainerInterface $container)
    {
        $this->legacyRouter = $legacyRouter;
        $this->parsedown = $parsedown;
        $this->container = $container;
        $this->emailParsedown = $emailParsedown;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('render_curl', function ($url) {
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

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('markdown', fn ($text) => $this->parsedown->text($text), ['is_safe' => ['html']]),
            new \Twig_SimpleFilter('markdown_email', fn ($text) => $this->emailParsedown->text($text), ['is_safe' => ['html']]),
        ];
    }

    public function getGlobals()
    {
        return [
            'legacy_router' => $this->legacyRouter,
            'google_analytics_enabled' => $this->container->getParameter('google_analytics_enabled'),
            'google_analytics_id' => $this->container->getParameter('google_analytics_id')
        ];
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'app';
    }
}
