<?php

namespace App\RendezVous\Admin\DeleteAttendee;

use Afup\Site\Logger\DbLoggerTrait;
use App\Action;
use App\RendezVous\RendezVousAttendeeRepository;
use Assert\Assertion;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DeleteAttendeeAction implements Action
{
    use DbLoggerTrait;

    /** @var FlashBagInterface */
    private $flashBag;
    /** @var UrlGeneratorInterface */
    private $urlGenerator;
    /** @var RendezVousAttendeeRepository */
    private $rendezVousAttendeeRepository;

    public function __construct(
        RendezVousAttendeeRepository $rendezVousAttendeeRepository,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->flashBag = $flashBag;
        $this->urlGenerator = $urlGenerator;
        $this->rendezVousAttendeeRepository = $rendezVousAttendeeRepository;
    }

    public function __invoke(Request $request)
    {
        $id = $request->query->getInt('id');
        try {
            $attendee = $this->rendezVousAttendeeRepository->find($id);
            Assertion::notNull($attendee);
            $this->rendezVousAttendeeRepository->delete($attendee);
            $this->log('Suppression de l\'inscrit '.$attendee->getId().' au rendez-vous');
            $this->flashBag->add('notice', 'L\'inscrit au rendez-vous a été supprimé');
        } catch (Exception $e) {
            $this->flashBag->add('error', 'Une erreur est survenue lors de la suppression de l\'inscrit au rendez-vous');
        }

        return new RedirectResponse($this->urlGenerator->generate('admin_rendezvous_list'));
    }
}
