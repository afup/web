<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsEventListener]
final readonly class RedirectEventFromSessionListener
{
    public const SESSION_KEY = 'event_selector_current_id';

    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        #[Autowire('%app.controllers_with_event_selector%')]
        private array $controllersWithEventSelector,
    ) {}

    public function __invoke(ControllerEvent $event): void
    {
        $controller = $event->getController();

        // Gestion des controllers avec plusieurs actions, dans ce cas le controller
        // est retourné sous ce format : [$controllerInstance, 'methodName']
        if (is_array($controller)) {
            $controller = $controller[0];
        }

        /**
         * Les controllers concernés sont calculés en amont par le container.
         * @see \AppBundle\DependencyInjection\ControllersWithEventSelectorPass
         */
        if (!is_object($controller) || !array_key_exists($controller::class, $this->controllersWithEventSelector)) {
            return;
        }

        $request = $event->getRequest();

        // Si on arrive sur une route en GET et qui a un sélecteur d'évènement,
        // alors on vérifie si un id d'évènement est présent dans la session et absent dans l'url.
        // Si c'est le cas, on redirige vers la même page, mais avec l'id en plus dans l'url.
        if (
            $request->isMethod('GET')
            && !$request->query->has('id')
            && $request->getSession()->has(self::SESSION_KEY)
        ) {
            $url = $this->urlGenerator->generate(
                $request->attributes->get('_route'),
                array_merge(
                    $request->attributes->get('_route_params'),
                    ['id' => $request->getSession()->get(self::SESSION_KEY)],
                ),
            );

            $event->setController(fn(): RedirectResponse => new RedirectResponse($url));
        }
    }
}
