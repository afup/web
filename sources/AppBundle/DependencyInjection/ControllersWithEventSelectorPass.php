<?php

declare(strict_types=1);

namespace AppBundle\DependencyInjection;

use AppBundle\Event\Form\Support\EventSelectFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Cette pass va chercher tous les controllers qui utilisent le sélecteur
 * d'évènement pour les ajouter dans une liste récupérable depuis le container.
 */
final readonly class ControllersWithEventSelectorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        // On récupère tous les controllers via un tag fourni par Symfony
        $services = $container->findTaggedServiceIds('controller.service_arguments');

        $controllers = [];

        foreach ($services as $id => $tags) {
            $definition = $container->getDefinition($id);
            $class = $definition->getClass();

            if ($class === null) {
                continue;
            }

            try {
                $reflectionClass = new \ReflectionClass($class);
                $constructor = $reflectionClass->getConstructor();

                if ($constructor === null) {
                    continue;
                }

                // On boucle sur les arguments du constructeur pour voir si le helper
                // du sélecteur est présent, si c'est le cas, on ajoute le controller à la liste.
                foreach ($constructor->getParameters() as $parameter) {
                    $type = $parameter->getType();

                    if ($type instanceof \ReflectionNamedType && $type->getName() === EventSelectFactory::class) {
                        $controllers[$class] = true;
                    }
                }
            } catch (\ReflectionException) {
                // S'il y a un souci (classe inexistante par exemple), on ignore l'erreur
                // pour éviter de planter tout le site.
            }
        }

        $container->setParameter('app.controllers_with_event_selector', $controllers);
    }
}
