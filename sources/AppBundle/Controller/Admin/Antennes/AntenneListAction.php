<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Antennes;

use AppBundle\Antennes\Entity\Repository\AntenneRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ANTENNES_READER')]
final readonly class AntenneListAction
{
    public function __construct(
        private AntenneRepository $antennesCollection,
        private Environment $twig,
    ) {}

    public function __invoke(Request $request): Response
    {
        return new Response($this->twig->render('admin/antennes/list.html.twig', [
            'antennes' => $this->antennesCollection->findBy([], ['libelle' => 'ASC']),
        ]));
    }
}
