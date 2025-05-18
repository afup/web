<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsEventListener]
final readonly class RedirectEventFromSessionListener
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
    ) {}

    public function __invoke(ControllerEvent $event): void
    {
        $controller = $event->getController();

        // Gestion des controllers avec plusieurs actions, dans ce cas le controller
        // est retourné sous ce format : [$controllerInstance, 'methodName']
        if (is_array($controller)) {
            $controller = $controller[0];
        }

        if (!$controller instanceof AdminActionWithEventSelector) {
            return;
        }

        $request = $event->getRequest();

        // Si on arrive sur une route en GET et qui a un sélecteur d'évènement,
        // alors on vérifie si un id d'évènement est présent dans la session et absent dans l'url.
        // Si c'est le cas, on redirige vers la même page, mais avec l'id en plus dans l'url.
        if (
            $request->isMethod('GET')
            && !$request->query->has('id')
            && $request->getSession()->has(AdminActionWithEventSelector::SESSION_KEY)
        ) {
            $url = $this->urlGenerator->generate(
                $request->attributes->get('_route'),
                array_merge(
                    $request->attributes->get('_route_params'),
                    ['id' => $request->getSession()->get(AdminActionWithEventSelector::SESSION_KEY)],
                ),
            );

            $event->setController(fn(): RedirectResponse => new RedirectResponse($url));
        }
    }
}
