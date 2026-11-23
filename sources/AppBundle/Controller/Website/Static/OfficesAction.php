<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Static;

use AppBundle\Antennes\Entity\Repository\AntenneRepository;
use AppBundle\Antennes\OfficesMapSvgGenerator;
use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class OfficesAction extends AbstractController
{
    public function __construct(
        private readonly ViewRenderer $view,
        private readonly AntenneRepository $antennesRepository,
        private readonly OfficesMapSvgGenerator $officesMapSvgGenerator,
    ) {}

    public function __invoke(): Response
    {
        $antennes = $this->antennesRepository->getAllSortedByLabels();

        return $this->view->render('site/offices.html.twig', [
            'antennes' => $antennes,
            // Généré à chaque affichage (moins d'une milliseconde) pour refléter tout de suite
            // les modifications faites dans l'admin ; les textes sont échappés par XMLWriter.
            'carteSvg' => $this->officesMapSvgGenerator->generate($antennes),
        ]);
    }
}
