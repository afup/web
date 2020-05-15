<?php

namespace AppBundle\Controller\RendezVous;

use Afup\Site\Logger\DbLoggerTrait;
use Afup\Site\Rendez_Vous;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class RendezVousArchiveAction
{
    use DbLoggerTrait;

    /** @var Rendez_Vous */
    private $rendezVous;
    /** @var Environment */
    private $twig;

    public function __construct(
        Rendez_Vous $rendezVous,
        Environment $twig
    ) {
        $this->rendezVous = $rendezVous;
        $this->twig = $twig;
    }

    public function __invoke(Request $request)
    {
        if ($request->query->has('id')) {
            $archiveRendezvous = $this->rendezVous->obtenirRendezVousPasse($request->query->getInt('id'));
        } else {
            $archiveRendezvous = $this->rendezVous->obtenirProchain();
        }

        if (!is_array($archiveRendezvous)) {
            return new Response($this->twig->render('legacy/rendezvous/pas-de-rendezvous.html.twig'));
        }

        $archiveRendezvous['date'] = date("d/m/Y", $archiveRendezvous['debut']);
        $archiveRendezvous['debut'] = date("H\hi", $archiveRendezvous['debut']);
        $archiveRendezvous['fin'] = date("H\hi", $archiveRendezvous['fin']);
        $params = [];
        $params['rendezvous'] = $archiveRendezvous;

        if ($this->rendezVous->accepteSurListeAttenteUniquement($archiveRendezvous['id'])) {
            $params['resultat'] = 'erreur';
            $params['message'] = 'Attention, les inscriptions sont closes. Votre inscription sera mise sur liste d\'attente. Si des places se lib�rent, vous recevrez un email.';
        }
        if ($this->rendezVous->estComplet($archiveRendezvous['id'])) {
            return new Response($this->twig->render('legacy/rendezvous/rendezvous-complet.html.twig', $params));
        }

        $formulaire = instancierFormulaire();

        $formulaire->addElement('hidden', 'id_rendezvous', $archiveRendezvous['id']);
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
            if ($this->rendezVous->enregistrerInscrit($formulaire)) {
                $this->log('Pré-inscription au prochain rendez-vous de ' . $formulaire->exportValue('nom'));

                return new Response($this->twig->render('legacy/rendezvous/message.html.twig', [
                        'resultat' => 'succes',
                        'message' => 'Votre pré-inscription a bien été prise en compte.',
                    ] + $params));
            }

            $params['resultat'] = 'erreur';
            $params['message'] = 'Il y a une erreur lors de votre pré-inscription. Merci de bien vouloir recommencer.';
        }

        return new Response($this->twig->render('legacy/rendezvous/archive-rendezvous.html.twig', [
                'formulaire' => genererFormulaire($formulaire),
            ] + $params));
    }
}
