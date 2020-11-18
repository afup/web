<?php

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class AppKernel extends Kernel
{
    use MicroKernelTrait;

    const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    public function registerBundles()
    {
        $bundles = [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new \Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),
            new \CCMBenchmark\TingBundle\TingBundle(),
            new \KnpU\OAuth2ClientBundle\KnpUOAuth2ClientBundle(),
            new AppBundle\AppBundle(),
            new \JMS\SerializerBundle\JMSSerializerBundle(),
            new Presta\SitemapBundle\PrestaSitemapBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return $this->getProjectDir().'/var/cache/'.$this->getEnvironment();
    }

    public function getLogDir()
    {
        return $this->getProjectDir().'/var/logs';
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader)
    {
        // Feel free to remove the "container.autowiring.strict_mode" parameter
        // if you are using symfony/dependency-injection 4.0+ as it's the default behavior
        $container->setParameter('container.autowiring.strict_mode', true);
        $container->setParameter('container.dumper.inline_class_loader', true);
        // TODO: replace with .env
        $loader->load($this->getProjectDir().'/app/config/parameters.yml');

        foreach ([$this->getProjectDir().'/app/config', $this->getProjectDir().'/sources/**/config'] as $confDir) {
            $loader->load($confDir.'/{packages}/*'.self::CONFIG_EXTS, 'glob');
            $loader->load($confDir.'/{packages}/'.$this->environment.'/**/*'.self::CONFIG_EXTS, 'glob');
            $loader->load($confDir.'/{services}'.self::CONFIG_EXTS, 'glob');
            $loader->load($confDir.'/{services}_'.$this->environment.self::CONFIG_EXTS, 'glob');
        }
    }

    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        foreach ([$this->getProjectDir().'/app/config', $this->getProjectDir().'/sources/**/config'] as $confDir) {
            // Load routes from each domain
            $routes->import($confDir.'/{routes}/*'.self::CONFIG_EXTS, '/', 'glob');
            $routes->import($confDir.'/{routes}/'.$this->environment.'/**/*'.self::CONFIG_EXTS, '/', 'glob');
            $routes->import($confDir.'/{routes}'.self::CONFIG_EXTS, '/', 'glob');
        }
    }
}
