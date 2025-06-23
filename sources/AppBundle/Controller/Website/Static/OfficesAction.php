<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Static;

use AppBundle\Antennes\AntennesCollection;
use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class OfficesAction extends AbstractController
{
    public function __construct(
        private readonly ViewRenderer $view,
    ) {}

    public function __invoke(): Response
    {
        return $this->view->render('site/offices.html.twig', [
            'antennes' => (new AntennesCollection())->getAllSortedByLabels(),
        ]);
    }
}
