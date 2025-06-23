<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Techletter;

use AppBundle\Twig\ViewRenderer;
use Symfony\Component\HttpFoundation\Response;

final readonly class IndexAction
{
    public function __construct(
        private ViewRenderer $view,
    ) {}

    public function __invoke(): Response
    {
        return $this->view->render('site/techletter/index.html.twig');
    }
}
