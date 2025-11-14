<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Antennes;

use AppBundle\Antennes\Antenne;
use AppBundle\Antennes\AntennesCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final readonly class AntenneListAction
{
    public function __construct(
        private AntennesCollection $antennesCollection,
        private Environment $twig,
    ) {}

    public function __invoke(Request $request): Response
    {
        $antennes = $this->antennesCollection->getAll();

        uasort(
            $antennes,
            fn(Antenne $a, Antenne $b) => strcmp($a->label, $b->label),
        );

        return new Response($this->twig->render('admin/antennes/list.html.twig', [
            'antennes' => $antennes,
        ]));
    }
}
