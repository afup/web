<?php

declare(strict_types=1);

use AppBundle\AppBundle;
use AppBundle\DependencyInjection\TingRepositoryPass;
use CCMBenchmark\TingBundle\TingBundle;
use Ekino\NewRelicBundle\EkinoNewRelicBundle;
use EWZ\Bundle\RecaptchaBundle\EWZRecaptchaBundle;
use JMS\SerializerBundle\JMSSerializerBundle;
use KnpU\OAuth2ClientBundle\KnpUOAuth2ClientBundle;
use Presta\SitemapBundle\PrestaSitemapBundle;
use Symfony\Bundle\DebugBundle\DebugBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Bundle\WebProfilerBundle\WebProfilerBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles(): array
    {
        $bundles = [
            new FrameworkBundle(),
            new SecurityBundle(),
            new TwigBundle(),
            new MonologBundle(),
            new TingBundle(),
            new KnpUOAuth2ClientBundle(),
            new AppBundle(),
            new JMSSerializerBundle(),
            new PrestaSitemapBundle(),
            new EWZRecaptchaBundle(),
            new EkinoNewRelicBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new DebugBundle();
            $bundles[] = new WebProfilerBundle();
        }

        return $bundles;
    }

    public function getProjectDir(): string
    {
        return __DIR__;
    }

    public function getCacheDir(): string
    {
        return dirname(__DIR__) . '/var/cache/' . $this->getEnvironment();
    }

    public function getLogDir(): string
    {
        return dirname(__DIR__) . '/var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/config/config_' . $this->getEnvironment() . '.yml');
    }

    protected function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new TingRepositoryPass());
    }
}
