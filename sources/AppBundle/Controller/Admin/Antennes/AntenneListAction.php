<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Antennes;

use AppBundle\Antennes\Entity\Antenne;
use AppBundle\Antennes\Entity\Repository\AntenneRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final readonly class AntenneListAction
{
    public function __construct(
        private AntenneRepository $antennesCollection,
        private Environment $twig,
    ) {}

    public function __invoke(Request $request): Response
    {
        $antennes = $this->antennesCollection->getAll();

        uasort(
            $antennes,
            fn(Antenne $a, Antenne $b): int => strcmp($a->libelle, $b->libelle),
        );

        return new Response($this->twig->render('admin/antennes/list.html.twig', [
            'antennes' => $antennes,
        ]));
    }
}
