<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Static;

use AppBundle\Antennes\AntenneRepository;
use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class OfficesAction extends AbstractController
{
    public function __construct(
        private readonly ViewRenderer $view,
        private readonly AntenneRepository $antennesRepository,
    ) {}

    public function __invoke(): Response
    {
        return $this->view->render('site/offices.html.twig', [
            'antennes' => $this->antennesRepository->getAllSortedByLabels(),
        ]);
    }
}
