<?php

namespace AppBundle\Controller\RendezVous;

use Afup\Site\Logger\DbLoggerTrait;
use Afup\Site\Rendez_Vous;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class RendezVousConfirmationAction
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
        $hash = $request->query->get('hash');
        $prochainRendezVous = $champs = null;
        if (null !== $hash) {
            $champs = $this->rendezVous->obtenirInscritAConfirmer($hash);
            if (null !== $champs['id_rendezvous'] && is_numeric($champs['id_rendezvous'])) {
                $prochainRendezVous = $this->rendezVous->obtenirRendezVousFutur($champs['id_rendezvous']);
            }
        }
        if (null === $prochainRendezVous) {
            $prochainRendezVous = $this->rendezVous->obtenirProchain();
        }

        if (!is_array($prochainRendezVous)) {
            return new Response($this->twig->render('legacy/rendezvous/pas-de-rendezvous.html.twig'));
        }
        $prochainRendezVous['date'] = date("d/m/Y", $prochainRendezVous['debut']);
        $prochainRendezVous['debut'] = date("H\hi", $prochainRendezVous['debut']);
        $prochainRendezVous['fin'] = date("H\hi", $prochainRendezVous['fin']);
        $params = ['rendezvous' => $prochainRendezVous];

        if (!is_array($champs)) {
            return new Response($this->twig->render('legacy/rendezvous/message.html.twig', [
                    'resultat' => 'erreur',
                    'message' => 'La confirmation n\'est pas possible. N\'avez-vous pas déjà précisé que vous ne veniez pas ?',
                ] + $params));
        }

        $formulaire = instancierFormulaire();
        $formulaire->setDefaults($champs);

        $formulaire->addElement('hidden', 'id', $champs['id']);
        $formulaire->addElement('hidden', 'id_rendezvous', $champs['id_rendezvous']);
        $formulaire->addElement('hidden', 'presence', $champs['presence']);

        $formulaire->addElement('header', '', 'Inscription');
        $formulaire->addElement('text', 'nom', 'Nom');
        $formulaire->addElement('text', 'prenom', 'Prénom');
        $formulaire->addElement('text', 'entreprise', 'Entreprise');
        $formulaire->addElement('text', 'email', 'Email');
        $formulaire->addElement('text', 'telephone', 'Téléphone');
        $formulaire->addElement('select', 'confirme', 'Confirmation', [
            null => '',
            AFUP_RENDEZ_VOUS_CONFIRME => 'OUI, je serai bien présent',
            AFUP_RENDEZ_VOUS_DECLINE => 'NON, je ne serai pas là finalement',
        ]);

        $formulaire->addElement('header', 'boutons', '');

        $formulaire->addElement('submit', 'soumettre', 'Modifier');

        $formulaire->addRule('nom', 'Nom manquant', 'required');
        $formulaire->addRule('email', 'Email manquant', 'required');
        $formulaire->addRule('email', 'Email invalide', 'email');
        $formulaire->addRule('telephone', 'Téléphone manquant', 'required');

        if ($formulaire->validate()) {
            if ($this->rendezVous->enregistrerConfirmationInscrit($formulaire)) {
                $this->log('Confirmation pour le prochain rendez-vous de ' . $formulaire->exportValue('nom'));

                return new Response($this->twig->render('legacy/rendezvous/message.html.twig', [
                        'resultat' => 'succes',
                        'message' => 'Votre confirmation a bien été prise en compte.',
                    ] + $params));
            }

            $params['resultat'] = 'erreur';
            $params['message'] = 'Il y a une erreur lors de votre confirmation. Merci de bien vouloir recommencer.';
        }

        return new Response($this->twig->render('legacy/rendezvous/confirmation.html.twig', [
                'formulaire' => genererFormulaire($formulaire),
            ] + $params));
    }
}
