<?php

declare(strict_types=1);

namespace AppBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class TingRepositoryPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $repositories = [];

        $tag = $container->getParameter('app.vendor.ting_repository');

        foreach ($container->findTaggedServiceIds($tag) as $id => $tags) {
            $repositories[$id] = $container->getDefinition($id);
        }

        $factoryService = 'ting';
        if (!$container->has($factoryService)) {
            throw new \RuntimeException(
                sprintf('Factory service "%s" not found. Please create it first.', $factoryService)
            );
        }

        foreach ($repositories as $definition) {
            $repositoryClass = $definition->getClass();

            $definition->setFactory([new Reference($factoryService), 'get']);

            $definition->setArguments([
                $repositoryClass,
            ]);
        }
    }
}
