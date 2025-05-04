<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Talk;

use AppBundle\Indexation\Talks\Runner;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class UpdateIndexationAction extends AbstractController
{
    public function __construct(private readonly Runner $runner)
    {
    }

    public function __invoke(Request $request): RedirectResponse
    {
        set_time_limit(240);

        $this->runner->run();

        $this->addFlash('notice', 'Indexation effectuée');

        return $this->redirect($request->headers->get('referer', '/pages/administration/index.php?page=forum_sessions'));
    }
}
