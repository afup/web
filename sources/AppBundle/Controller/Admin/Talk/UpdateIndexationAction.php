<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Talk;

use AppBundle\Indexation\Talks\Runner;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class UpdateIndexationAction
{
    private FlashBagInterface $flashBag;
    private Runner $runner;

    public function __construct(
        Runner $runner,
        FlashBagInterface $flashBag
    ) {
        $this->runner = $runner;
        $this->flashBag = $flashBag;
    }

    public function __invoke(Request $request): RedirectResponse
    {
        set_time_limit(240);

        $this->runner->run();
        $this->flashBag->add('notice', 'Indexation effectuée');

        return new RedirectResponse($request->headers->get('referer', '/pages/administration/index.php?page=forum_sessions'));
    }
}
