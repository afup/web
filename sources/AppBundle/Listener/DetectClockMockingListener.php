<?php

declare(strict_types=1);

namespace AppBundle\Listener;

use Symfony\Component\Clock\Clock;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;

#[AsEventListener]
final class DetectClockMockingListener
{
    public const HEADER = 'X-Test-Mock-Clock';

    public function __construct(
        #[Autowire('%kernel.environment%')]
        private readonly string $env,
    ) {}

    public function __invoke(RequestEvent $event): void
    {
        if ($this->env !== 'test') {
            return;
        }

        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        if (!$request->headers->has(self::HEADER)) {
            return;
        }

        $headerValue = $request->headers->get(self::HEADER);

        if ($headerValue === null || $headerValue === '') {
            return;
        }

        Clock::set(new MockClock($headerValue));
    }
}
