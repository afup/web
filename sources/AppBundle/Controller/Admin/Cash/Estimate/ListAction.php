<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Cash\Estimate;

use AppBundle\Cash\Model\Repository\AfupInvoiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ListAction extends AbstractController
{
    public function __construct(private readonly AfupInvoiceRepository $afupInvoiceRepository) {}

    public function __invoke(): Response
    {
        //TODO : à supprimer quand les actions via le formulaire auront été migée
        if (isset($_SESSION['flash']['message'])) {
            $this->addFlash('notice', $_SESSION['flash']['message']);
        }
        if (isset($_SESSION['flash']['erreur'])) {
            $this->addFlash('error', $_SESSION['flash']['erreur']);
        }
        unset($_SESSION['flash']);
        $list = $this->afupInvoiceRepository->getList();
        return $this->render('admin/cash/estimate/list.html.twig', [
            'estimates' => $list,
        ]);
    }
}
