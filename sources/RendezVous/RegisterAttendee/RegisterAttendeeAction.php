<?php

namespace App\RendezVous\RegisterAttendee;

use Afup\Site\Logger\DbLoggerTrait;
use App\Action;
use App\RendezVous\RendezVousRepository;
use App\RendezVous\RendezVousService;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class RegisterAttendeeAction implements Action
{
    use DbLoggerTrait;

    /** @var Environment */
    private $twig;
    /** @var RendezVousService */
    private $rendezVousService;
    /** @var RendezVousRepository */
    private $rendezVousRepository;

    public function __construct(
        RendezVousRepository $rendezVousRepository,
        RendezVousService $rendezVousService,
        Environment $twig
    ) {
        $this->twig = $twig;
        $this->rendezVousService = $rendezVousService;
        $this->rendezVousRepository = $rendezVousRepository;
    }

    public function __invoke(Request $request)
    {
        if ($request->query->has('id')) {
            $rendezVous = $this->rendezVousRepository->find($request->query->getInt('id'));
        } else {
            $rendezVous = $this->rendezVousRepository->findNext();
        }
        if (null === $rendezVous) {
            return new Response($this->twig->render('legacy/rendezvous/pas-de-rendezvous.html.twig'));
        }
        $archiveRendezvous['date'] = date("d/m/Y", $rendezVous->getStart());
        $archiveRendezvous['debut'] = date("H\hi", $rendezVous->getStart());
        $archiveRendezvous['fin'] = date("H\hi", $rendezVous->getEnd());
        $params = [];
        $params['rendezvous'] = $archiveRendezvous;

        if ($this->rendezVousService->accepteSurListeAttenteUniquement($rendezVous)) {
            $params['resultat'] = 'erreur';
            $params['message'] = 'Attention, les inscriptions sont closes. Votre inscription sera mise sur liste d\'attente. Si des places se lib�rent, vous recevrez un email.';
        }
        if ($this->rendezVousService->isFull($rendezVous)) {
            return new Response($this->twig->render('legacy/rendezvous/rendezvous-complet.html.twig', $params));
        }

        $formulaire = instancierFormulaire();

        $formulaire->addElement('hidden', 'id_rendezvous', $rendezVous->getId());
        $formulaire->addElement('hidden', 'id', 0);
        $formulaire->addElement('hidden', 'creation', time());
        $formulaire->addElement('hidden', 'presence', 0);
        $formulaire->addElement('hidden', 'confirme', 0);

        $formulaire->addElement('header', '', 'Inscription');
        $formulaire->addElement('text', 'nom', 'Nom');
        $formulaire->addElement('text', 'entreprise', 'Entreprise');
        $formulaire->addElement('text', 'email', 'Email');
        $formulaire->addElement('text', 'telephone', 'T�l�phone');
        $formulaire->addElement('submit', 'soumettre', 'S\'inscrire');

        $formulaire->addRule('nom', 'Nom manquant', 'required');
        $formulaire->addRule('email', 'Email manquant', 'required');
        $formulaire->addRule('email', 'Email invalide', 'email');
        $formulaire->addRule('telephone', 'Téléphone manquant', 'required');

        if ($formulaire->validate()) {
            try {
                $this->rendezVousService->registerAttendee($formulaire);
                $this->log('Pré-inscription au prochain rendez-vous de '.$formulaire->exportValue('nom'));

                return new Response($this->twig->render('legacy/rendezvous/message.html.twig', [
                        'resultat' => 'succes',
                        'message' => 'Votre pré-inscription a bien été prise en compte.',
                    ] + $params));
            } catch (Exception $e) {
                $params['resultat'] = 'erreur';
                $params['message'] = 'Il y a une erreur lors de votre pré-inscription. Merci de bien vouloir recommencer.';
            }
        }

        return new Response($this->twig->render('legacy/rendezvous/archive-rendezvous.html.twig', [
                'formulaire' => genererFormulaire($formulaire),
            ] + $params));
    }
}
