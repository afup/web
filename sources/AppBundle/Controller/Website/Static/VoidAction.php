<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Static;

use AppBundle\Twig\ViewRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class VoidAction
{
    public function __construct(
        private ViewRenderer $view,
    ) {}

    public function __invoke(Request $request): Response
    {
        $params = [];
        if ($request->attributes->has('legacyContent')) {
            $params = $request->attributes->get('legacyContent');
        }

        return $this->view->render('site/base.html.twig', $params);
    }
}
