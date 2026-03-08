<?php

declare(strict_types=1);

namespace AppBundle\Controller\Legacy;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;
use Twig\Error\LoaderError;
use Webmozart\Assert\Assert;

class LegacyEventAction
{
    public function __construct(private readonly Environment $twig) {}

    public function __invoke(Request $request): Response
    {
        $year = $request->attributes->getInt('year');
        $page = str_replace('.php', '', $request->attributes->get('page'));
        try {
            Assert::inArray($year, [2005, 2006, 2007, 2008, 2009]);
            Assert::regex($page, '/[a-z0-9_]+/');
            $template = $this->twig->load(sprintf('legacy/forumphp%d/%s.html.twig', $year, $page));
        } catch (InvalidArgumentException|LoaderError) {
            throw new NotFoundHttpException('Page introuvable');
        }

        return new Response($template->render());
    }
}
