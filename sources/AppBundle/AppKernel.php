<?php

declare(strict_types=1);

namespace AppBundle;

use AppBundle\DependencyInjection\ControllersWithEventSelectorPass;
use AppBundle\DependencyInjection\TingRepositoryPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    use MicroKernelTrait;

    public function getProjectDir(): string
    {
        return __DIR__ . '/../../app';
    }

    public function getCacheDir(): string
    {
        return __DIR__ . '/../../var/cache/' . $this->getEnvironment();
    }

    public function getLogDir(): string
    {
        return __DIR__ . '/../../var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../../app/config/config_' . $this->getEnvironment() . '.yml');
    }

    protected function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new TingRepositoryPass());
        $container->addCompilerPass(new ControllersWithEventSelectorPass());
    }
}
