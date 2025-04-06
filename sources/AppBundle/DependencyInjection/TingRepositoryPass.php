<?php

declare(strict_types=1);

namespace AppBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Cette classe enregistre tous les repositories Ting automatiquement dans
 * le container en services injectables.
 */
final class TingRepositoryPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $factoryService = 'ting';
        if (!$container->has($factoryService)) {
            // Si Ting n'est pas présent dans le container, c'est soit que le container
            // est mal configuré, soit que Ting n'est plus utilisé.
            throw new \RuntimeException('Ting service manquant dans le container');
        }

        $repositories = [];

        // Chaque repository est tagué automatiquement par le container via la config `_instanceof`
        foreach ($container->findTaggedServiceIds('app.vendor.ting_repository') as $id => $tags) {
            $repositories[$id] = $container->getDefinition($id);
        }

        // Et enfin, chaque repository est enregistré dans le container en tant que service,
        // et instancié via la factory Ting.
        foreach ($repositories as $definition) {
            $repositoryClass = $definition->getClass();

            $definition->setFactory([new Reference($factoryService), 'get']);
            $definition->setArguments([$repositoryClass]);

            // À supprimer une fois qu'il n'y aura plus de pages à l'ancienne
            $definition->setPublic(true);
        }
    }
}
