<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin;

use Assert\Assertion;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class GetMenuAction
{
    /** @var array<string, mixed> */
    private array $backOfficePages;
    private Environment $twig;
    private RequestStack $requestStack;

    public function __construct(
        RequestStack $requestStack,
        Environment $twig,
        array $backOfficePages
    ) {
        $this->backOfficePages = $backOfficePages;
        $this->requestStack = $requestStack;
        $this->twig = $twig;
    }

    public function __invoke(): Response
    {
        $masterRequest = $this->requestStack->getMasterRequest();
        Assertion::notNull($masterRequest);
        $page = $masterRequest->query->get('page');
        $route = $masterRequest->get('_route');

        $currentGroupKey = null;
        $currentElementKey = null;

        foreach ($this->backOfficePages as $groupKey => $group) {
            if (isset($group['elements'])) {
                foreach ($group['elements'] as $elementKey => $element) {
                    if ($elementKey === $page
                        || (isset($element['extra_routes']) && in_array($route, $element['extra_routes'], true))
                        || (isset($element['extra_pages']) && in_array($page, $element['extra_pages'], true))
                    ) {
                        $currentGroupKey = $groupKey;
                        $currentElementKey = $elementKey;
                    }
                }
            }
        }

        return new Response($this->twig->render(':admin:menu.html.twig', [
            'pages' => $this->backOfficePages,
            'current_group_key' => $currentGroupKey,
            'current_element_key' => $currentElementKey,
        ]));
    }
}
