<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Static;

use AppBundle\SuperApero\Entity\Repository\SuperAperoRepository;
use AppBundle\Twig\ViewRenderer;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final readonly class SuperAperoAction
{
    public function __construct(
        private ViewRenderer $view,
        private SuperAperoRepository $superAperoRepository,
        private UrlGeneratorInterface $urlGenerator,
    ) {}

    public function __invoke(): Response
    {
        $superApero = $this->superAperoRepository->findActive();

        if ($superApero === null) {
            return new RedirectResponse($this->urlGenerator->generate('home'));
        }

        return $this->view->render('site/superapero.html.twig', [
            'superApero' => $superApero,
        ]);
    }
}
