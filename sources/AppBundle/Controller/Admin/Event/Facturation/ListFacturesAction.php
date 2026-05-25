<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event\Facturation;

use AppBundle\Event\AdminEventSelection;
use AppBundle\Event\Model\Repository\InvoiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

class ListFacturesAction extends AbstractController
{
    public const array VALID_SORTS = ['date_facture', 'reference', 'societe', 'montant', 'etat', 'facturation'];
    public const array VALID_DIRECTIONS = ['asc', 'desc'];

    public function __construct(private readonly InvoiceRepository $invoiceRepository) {}

    public function __invoke(AdminEventSelection $eventSelection, Request $request): Response
    {
        $event = $eventSelection->event;
        $sort = $request->query->get('sort', 'date_facture');
        $direction = $request->query->get('direction', 'desc');
        Assert::inArray($sort, self::VALID_SORTS);
        Assert::inArray($direction, self::VALID_DIRECTIONS);
        $filter = $request->query->get('filter', '');
        $invoices = $this->invoiceRepository->getByEventId($event->getId(), $sort, $direction, $filter);

        return $this->render('admin/event/facturation/list.html.twig', [
            'event' => $event,
            'event_select_form' => $eventSelection->selectForm(),
            'direction' => $direction,
            'invoices' => $invoices,
            'sort' => $sort,
            'filter' => $filter,
        ]);
    }
}
