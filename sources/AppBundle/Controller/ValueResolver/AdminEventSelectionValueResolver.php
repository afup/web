<?php

declare(strict_types=1);

namespace AppBundle\Controller\ValueResolver;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\AdminEventSelection;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

#[AsTaggedItem(index: 'admin_event_selection', priority: 150)]
final readonly class AdminEventSelectionValueResolver implements ValueResolverInterface
{
    public function __construct(
        private EventActionHelper $eventActionHelper,
    ) {}

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $argumentType = $argument->getType();

        if ($argumentType !== AdminEventSelection::class) {
            return [];
        }

        return [
            $this->eventActionHelper->getFromRequest('id'),
        ];
    }
}
