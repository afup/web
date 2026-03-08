<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\SuperApero;

use AppBundle\SuperApero\Entity\Repository\SuperAperoRepository;
use AppBundle\SuperApero\Entity\SuperApero;
use Psr\Clock\ClockInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class ListAction extends AbstractController
{
    public function __construct(
        private readonly SuperAperoRepository $superAperoRepository,
        private readonly ClockInterface $clock,
    ) {}

    public function __invoke(): Response
    {
        $currentYear = (int) $this->clock->now()->format('Y');

        return $this->render('admin/super_apero/index.html.twig', [
            'aperos' => $this->superAperoRepository->getAllSortedByYear(),
            'currentYear' => $currentYear,
            'hasSuperAperoForCurrentYear' => $this->superAperoRepository->findOneByYear($currentYear) instanceof SuperApero,
        ]);
    }
}
