<?php

namespace AppBundle\Twig;

use Afup\Site\Corporate\Page;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;

class ViewRenderer
{
    private Security $security;
    private RequestStack $requestStack;
    private Environment $twig;

    public function __construct(Security $security, RequestStack $requestStack, Environment $twig)
    {
        $this->security = $security;
        $this->requestStack = $requestStack;
        $this->twig = $twig;
    }

    public function render(string $view, array $parameters = []): Response
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
                'footer' => $page->footer()
            ];
        }

        $content = $this->twig->render($view, $parameters + $blocks);

        $response = new Response();
        return $response->setContent($content);
    }
}
