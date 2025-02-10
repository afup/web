<?php

declare(strict_types=1);

namespace AppBundle\Controller\Legacy;

use Assert\Assertion;
use Assert\AssertionFailedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;
use Twig\Error\LoaderError;

class LegacyEventAction
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function __invoke(Request $request): Response
    {
        $year = $request->attributes->getInt('year');
        $page = str_replace('.php', '', $request->attributes->get('page'));
        try {
            Assertion::inArray($year, [2005, 2006, 2007, 2008, 2009]);
            Assertion::regex($page, '/[a-z0-9_]+/');
            $template = $this->twig->load(sprintf('legacy/forumphp%d/%s.html.twig', $year, $page));
        } catch (AssertionFailedException|LoaderError $e) {
            throw new NotFoundHttpException('Page introuvable');
        }

        return new Response($template->render());
    }
}
