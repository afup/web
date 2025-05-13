<?php

declare(strict_types=1);

namespace AppBundle\Twig;

use Afup\Site\Corporate\Page;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ViewRenderer
{
    public function __construct(
        private readonly Security $security,
        private readonly RequestStack $requestStack,
        private readonly Environment $twig,
    ) {
    }


    /**
     * Renders a view.
     *
     * @param string        $view       The view name
     * @param array         $parameters An array of parameters to pass to the view
     * @param Response|null $response   A response instance
     *
     * @return Response A Response instance
     */
    public function render(string $view, array $parameters = [], Response $response = null): Response
    {
        $blocks = [];
        if ($this->requestStack->getCurrentRequest()) {
            $requestUri = $this->requestStack->getCurrentRequest()->getRequestUri();
            $page = new Page();
            $blocks = [
                'community' => $page->community(),
                'header' => $page->header($requestUri, $this->security->getUser()),
                'sidebar' => $page->getRightColumn(),
                'social' => $page->social(),
                'footer' => $page->footer(),
            ];
        }

        $content = $this->twig->render($view, $parameters + $blocks);

        if (!$response instanceof Response) {
            $response = new Response();
        }

        return $response->setContent($content);
    }
}
