<?php

declare(strict_types=1);

use AppBundle\AppBundle;
use CCMBenchmark\TingBundle\TingBundle;
use Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle;
use Ekino\Bundle\NewRelicBundle\EkinoNewRelicBundle;
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
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new FrameworkBundle(),
            new SecurityBundle(),
            new TwigBundle(),
            new MonologBundle(),
            new DoctrineCacheBundle(),
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

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return dirname(__DIR__) . '/var/cache/' . $this->getEnvironment();
    }

    public function getLogDir()
    {
        return dirname(__DIR__) . '/var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load($this->getRootDir() . '/config/config_' . $this->getEnvironment() . '.yml');
    }
}
