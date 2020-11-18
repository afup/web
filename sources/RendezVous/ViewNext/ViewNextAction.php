<?php

namespace App\RendezVous\ViewNext;

use Afup\Site\Logger\DbLoggerTrait;
use App\Action;
use App\RendezVous\RendezVousRepository;
use App\RendezVous\RendezVousService;
use App\RendezVous\RendezVousSlideRepository;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ViewNextAction implements Action
{
    use DbLoggerTrait;

    /** @var Environment */
    private $twig;
    /** @var RendezVousRepository */
    private $rendezVousRepository;
    /** @var RendezVousService */
    private $rendezVousService;
    /** @var RendezVousSlideRepository */
    private $rendezVousSlideRepository;

    public function __construct(
        RendezVousRepository $rendezVousRepository,
        RendezVousSlideRepository $rendezVousSlideRepository,
        RendezVousService $rendezVousService,
        Environment $twig
    ) {
        $this->twig = $twig;
        $this->rendezVousRepository = $rendezVousRepository;
        $this->rendezVousService = $rendezVousService;
        $this->rendezVousSlideRepository = $rendezVousSlideRepository;
    }

    public function __invoke(Request $request)
    {
        $id = $request->query->has('id') ? $request->query->getInt('id') : null;
        $rendezVous = null !== $id ? $this->rendezVousRepository->find($id) : $this->rendezVousRepository->findNext();
        if (null === $rendezVous) {
            return new Response($this->twig->render('legacy/rendezvous/pas-de-rendezvous.html.twig'));
        }
        $params = [];
        $nextRendezVous['est_futur'] = $rendezVous->getStart() > time();
        $nextRendezVous['date'] = date("d/m/Y", $rendezVous->getStart());
        $nextRendezVous['debut'] = date("H\hi", $rendezVous->getStart());
        $nextRendezVous['fin'] = date("H\hi", $rendezVous->getEnd());

        if (null !== $id) {
            foreach ($this->rendezVousSlideRepository->findByRendezVous($rendezVous) as $i => $slide) {
                $nextRendezVous['slides'.$i] = $slide['fichier'];
                $nextRendezVous['urlslides'.$i] = $slide['url'];
            }
        }

        $params['rendezvous'] = $nextRendezVous;

        if (!$nextRendezVous['est_futur']) {
            return new Response($this->twig->render('legacy/rendezvous/rendezvous-archive.html.twig', $params));
        }
        if ($this->rendezVousService->accepteSurListeAttenteUniquement($rendezVous)) {
            $params['resultat'] = 'erreur';
            $params['message'] = 'Attention, les inscriptions sont closes. Votre inscription sera mise sur liste d\'attente. Si des places se libèrent, vous recevrez un email.';
        }
        if ($this->rendezVousService->isFull($rendezVous)) {
            return new Response($this->twig->render('legacy/rendezvous/rendezvous-complet.html.twig', $params));
        }
        if (!$rendezVous->isRegistration() && !$rendezVous->getExternalUrl()) {
            $params['message_inscription'] = 'Entrée libre';
        } elseif (!$rendezVous->isRegistration() && $rendezVous->getExternalUrl()) {
            $params['message_inscription'] = 'Les pré-inscriptions s\'effectuent en cliquant sur le lien ci-dessous';
        } else {
            $formulaire = instancierFormulaire();

            $formulaire->addElement('hidden', 'id_rendezvous', $rendezVous->getId());
            $formulaire->addElement('hidden', 'id', 0);
            $formulaire->addElement('hidden', 'creation', time());
            $formulaire->addElement('hidden', 'presence', 0);
            $formulaire->addElement('hidden', 'confirme', 0);

            $formulaire->addElement('header', '', 'Inscription');
            $formulaire->addElement('text', 'nom', 'Nom');
            $formulaire->addElement('text', 'prenom', 'Prénom');
            $formulaire->addElement('text', 'entreprise', 'Entreprise');
            $formulaire->addElement('text', 'email', 'Email');
            $formulaire->addElement('text', 'telephone', 'Téléphone');
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

            $params['formulaire'] = genererFormulaire($formulaire);
        }

        return new Response($this->twig->render('legacy/rendezvous/prochain-rendezvous.html.twig', $params));
    }
}
