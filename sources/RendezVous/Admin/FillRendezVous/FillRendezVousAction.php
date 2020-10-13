<?php

namespace App\RendezVous\Admin\FillRendezVous;

use Afup\Site\Logger\DbLoggerTrait;
use App\Action;
use App\RendezVous\RendezVousRepository;
use App\RendezVous\RendezVousService;
use Assert\Assertion;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FillRendezVousAction implements Action
{
    use DbLoggerTrait;

    /** @var FlashBagInterface */
    private $flashBag;
    /** @var UrlGeneratorInterface */
    private $urlGenerator;
    /** @var RendezVousRepository */
    private $rendezVousRepository;
    /** @var RendezVousService */
    private $rendezVousService;

    public function __construct(
        RendezVousRepository $rendezVousRepository,
        RendezVousService $rendezVousService,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->flashBag = $flashBag;
        $this->urlGenerator = $urlGenerator;
        $this->rendezVousRepository = $rendezVousRepository;
        $this->rendezVousService = $rendezVousService;
    }

    public function __invoke(Request $request)
    {
        $id = $request->query->getInt('id');

        try {
            $rendezVous = $this->rendezVousRepository->find($id);
            Assertion::notNull($rendezVous);
            $this->rendezVousService->fillWithWaitingList($rendezVous);
            $this->log('Remplissage du rendez-vous avec la liste d\'attente');
            $this->flashBag->add('notice', 'Le remplissage avec la liste d\'attente a été effectué');
        } catch (Exception $e) {
            $this->flashBag->add('error', 'Une erreur est survenue lors du remplissage avec la liste d\'attente pour le prochain rendez-vous');
        }

        return new RedirectResponse($this->urlGenerator->generate('admin_rendezvous_list'));
    }
}
