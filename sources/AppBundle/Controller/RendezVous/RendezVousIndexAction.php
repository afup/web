<?php

namespace AppBundle\Controller\RendezVous;

use Afup\Site\Logger\DbLoggerTrait;
use Afup\Site\Rendez_Vous;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class RendezVousIndexAction
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
        $id = $request->query->has('id') ? $request->query->getInt('id') : null;
        $nextRendezVous = null !== $id ? $this->rendezVous->obtenir($id) : $this->rendezVous->obtenirProchain();

        if (!is_array($nextRendezVous)) {
            return new Response($this->twig->render('legacy/rendezvous/pas-de-rendezvous.html.twig'));
        }
        $params = [];
        if ($nextRendezVous['debut'] <= time()) {
            $nextRendezVous['est_futur'] = false;
        } else {
            $nextRendezVous['est_futur'] = true;
        }

        $nextRendezVous['date'] = date("d/m/Y", $nextRendezVous['debut']);
        $nextRendezVous['debut'] = date("H\hi", $nextRendezVous['debut']);
        $nextRendezVous['fin'] = date("H\hi", $nextRendezVous['fin']);

        if (null !== $id) {
            $champsSlides = $this->rendezVous->obtenirSlides($id);
            foreach ($champsSlides as $i => $champSlide) {
                $nextRendezVous['slides' . $i] = $champSlide['fichier'];
                $nextRendezVous['urlslides' . $i] = $champSlide['url'];
            }
        }

        $params['rendezvous'] = $nextRendezVous;

        if (!$nextRendezVous['est_futur']) {
            return new Response($this->twig->render('legacy/rendezvous/rendezvous-archive.html.twig', $params));
        }
        if ($this->rendezVous->accepteSurListeAttenteUniquement($nextRendezVous['id'])) {
            $params['resultat'] = 'erreur';
            $params['message'] = 'Attention, les inscriptions sont closes. Votre inscription sera mise sur liste d\'attente. Si des places se libèrent, vous recevrez un email.';
        }
        if ($this->rendezVous->estComplet($nextRendezVous['id'])) {
            return new Response($this->twig->render('legacy/rendezvous/rendezvous-complet.html.twig', $params));
        }

        if ($nextRendezVous['inscription'] == '0' && empty($nextRendezVous['url_externe'])) {
            $params['message_inscription'] = 'Entrée libre';
        } elseif ($nextRendezVous['inscription'] == '0' && $nextRendezVous['url_externe'] != '') {
            $params['message_inscription'] = 'Les pré-inscriptions s\'effectuent en cliquant sur le lien ci-dessous';
        } else {
            $formulaire = instancierFormulaire();

            $formulaire->addElement('hidden', 'id_rendezvous', $nextRendezVous['id']);
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
                $ok = $this->rendezVous->enregistrerInscrit($formulaire);
                if ($ok) {
                    $this->log('Pré-inscription au prochain rendez-vous de ' . $formulaire->exportValue('nom'));

                    return new Response($this->twig->render('legacy/rendezvous/message.html.twig', [
                            'resultat' => 'succes',
                            'message' => 'Votre pré-inscription a bien été prise en compte.',
                        ] + $params));
                }

                $params['resultat'] = 'erreur';
                $params['message'] = 'Il y a une erreur lors de votre pré-inscription. Merci de bien vouloir recommencer.';
            }

            $params['formulaire'] = genererFormulaire($formulaire);
        }

        return new Response($this->twig->render('legacy/rendezvous/prochain-rendezvous.html.twig', $params));
    }
}
