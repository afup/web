<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Entity\BilleteriePrivee;
use AppBundle\Event\Entity\Repository\BilleteriePriveeRepository;
use AppBundle\Event\Entity\Repository\TicketTypeRepository;
use AppBundle\Event\Form\BilleteriePriveeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EditBilleteriePriveeAction extends AbstractController
{
    public function __construct(
        private readonly EventActionHelper $eventActionHelper,
        private readonly BilleteriePriveeRepository $billeteriePriveeRepository,
        private readonly TicketTypeRepository $ticketTypeRepository,
    ) {}

    public function __invoke(Request $request, int $event, int $id): Response
    {
        $event = $this->eventActionHelper->getEventById($event);

        $billeteriePrivee = $this->billeteriePriveeRepository->find($id);
        if (!$billeteriePrivee instanceof BilleteriePrivee || $billeteriePrivee->eventId !== $event->getId()) {
            throw $this->createNotFoundException(sprintf('Impossible de trouver la billeterie privée ayant l\'identifiant : %s', $id));
        }

        $maxPlacesActuelles = $billeteriePrivee->maxPlaces;

        $form = $this->createForm(BilleteriePriveeType::class, $billeteriePrivee, [
            'ticketTypes' => $this->ticketTypeRepository->findAllOrderedById(),
            'is_edit' => true,
        ]);

        $form->handleRequest($request);

        $placesPrises = null;

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $motDePasse = $form->get('plainMotDePasse')->getData();
                if (is_string($motDePasse) && $motDePasse !== '') {
                    $billeteriePrivee->motDePasse = password_hash($motDePasse, PASSWORD_DEFAULT);
                }
            }

            // Protection du quota : impossible de réduire sous le nombre de places déjà prises.
            // Le comptage n'est fait que si le champ maxPlaces a été modifié
            $maxPlacesSoumises = $form->get('maxPlaces')->getData();
            if (is_int($maxPlacesSoumises) && $maxPlacesSoumises < $maxPlacesActuelles) {
                $placesPrises = $this->billeteriePriveeRepository->countPlacesPrisesParToken($billeteriePrivee->token);
                if ($maxPlacesSoumises < $placesPrises) {
                    $form->get('maxPlaces')->addError(new FormError(sprintf(
                        '%d place(s) déjà prise(s) : le nombre de places ne peut pas être réduit en dessous.',
                        $placesPrises,
                    )));
                }
            }

            if ($form->isValid()) {
                $this->billeteriePriveeRepository->save($billeteriePrivee);

                $this->addFlash('notice', 'La billeterie privée a été modifiée');

                return $this->redirectToRoute('admin_event_billeterie_privee', [
                    'id' => $event->getId(),
                ]);
            }
        } else {
            // Pas de soumission : on affiche le compteur de places prises
            $placesPrises = $this->billeteriePriveeRepository->countPlacesPrisesParToken($billeteriePrivee->token);
        }

        return $this->render('admin/event/billeterie_privee_edit.html.twig', [
            'is_new' => false,
            'billeterie_privee' => $billeteriePrivee,
            'places_prises' => $placesPrises,
            'event' => $event,
            'title' => 'Billeteries privées - Modifier',
            'edit_title' => $billeteriePrivee->nom,
            'form' => $form->createView(),
        ]);
    }
}
