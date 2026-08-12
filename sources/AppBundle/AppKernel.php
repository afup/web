<?php

declare(strict_types=1);

namespace AppBundle;

use AppBundle\DependencyInjection\ControllersWithEventSelectorPass;
use AppBundle\DependencyInjection\TingRepositoryPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    use MicroKernelTrait;

    #[\Override]
    public function getProjectDir(): string
    {
        return __DIR__ . '/../..';
    }

    #[\Override]
    public function getCacheDir(): string
    {
        return $this->getProjectDir() . '/var/cache/' . $this->getEnvironment();
    }

    #[\Override]
    public function getLogDir(): string
    {
        return $this->getProjectDir() . '/var/logs';
    }

    protected function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new TingRepositoryPass());
        $container->addCompilerPass(new ControllersWithEventSelectorPass());
    }
}
