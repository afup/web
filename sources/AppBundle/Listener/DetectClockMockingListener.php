<?php

declare(strict_types=1);

namespace AppBundle\Listener;

use Afup\Tests\Support\TimeMocker;
use Symfony\Component\Clock\Clock;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Event\RequestEvent;

final readonly class DetectClockMockingListener
{
    public function __construct(
        #[Autowire('%kernel.environment%')]
        private string $env,
    ) {}

    public function __invoke(RequestEvent $event): void
    {
        if ($this->env !== 'test') {
            // Le listener est configuré manuellement uniquement dans l'env de test.
            // Mais on ne sait jamais alors, alors on vérifie ici au cas où.
            return;
        }

        $currentDateMock = (new TimeMocker())->getCurrentDateMock();
        if ($currentDateMock === null) {
            return;
        }

        Clock::set(new MockClock($currentDateMock));
    }
}
