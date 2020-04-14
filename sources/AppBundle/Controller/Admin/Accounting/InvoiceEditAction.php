<?php

namespace AppBundle\Controller\Admin\Accounting;

use Afup\Site\Comptabilite\Facture;
use Afup\Site\Utils\Pays;
use AppBundle\Controller\Admin\BackOfficeLegacyBridge;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig_Environment;

class InvoiceEditAction
{
    const ACTION_EDIT = 'modifier';
    const ACTION_ADD = 'ajouter';
    const ACTIONS = [
        self::ACTION_EDIT,
    ];
    /** @var Twig_Environment */
    private $twig;
    /** @var BackOfficeLegacyBridge */
    private $backOfficeLegacyBridge;
    /** @var UrlGeneratorInterface */
    private $urlGenerator;
    /** @var SessionInterface */
    private $session;

    public function __construct(
        Twig_Environment $twig,
        BackOfficeLegacyBridge $backOfficeLegacyBridge,
        SessionInterface $session,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->twig = $twig;
        $this->backOfficeLegacyBridge = $backOfficeLegacyBridge;
        $this->urlGenerator = $urlGenerator;
        $this->session = $session;
    }

    public function __invoke(Request $request)
    {
        global $bdd;
        $response = $this->backOfficeLegacyBridge->handlePage('compta_facture');
        if (null !== $response) {
            return $response;
        }

        $action = $request->query->get('action');
        if (!in_array($action, self::ACTIONS, true)) {
            $action = self::ACTION_EDIT;
        }

        $comptaFact = new Facture($bdd);
        $form = null;
        $factureId = null;
        $ok = false;
        $champs = [];
        $id = $request->attributes->get('id');

        $pays = new Pays($bdd);
        $form = instancierFormulaire();
        if ($action === self::ACTION_EDIT) {
            $champsRecup = $comptaFact->obtenir($id);
            $factureId = $champsRecup['id'];
            $champs['date_facture'] = $champsRecup['date_facture'];
            $champs['societe'] = $champsRecup['societe'];
            $champs['service'] = $champsRecup['service'];
            $champs['adresse'] = $champsRecup['adresse'];
            $champs['code_postal'] = $champsRecup['code_postal'];
            $champs['ville'] = $champsRecup['ville'];
            $champs['id_pays'] = $champsRecup['id_pays'];
            $champs['email'] = $champsRecup['email'];
            $champs['observation'] = $champsRecup['observation'];
            $champs['ref_clt1'] = $champsRecup['ref_clt1'];
            $champs['ref_clt2'] = $champsRecup['ref_clt2'];
            $champs['ref_clt3'] = $champsRecup['ref_clt3'];
            $champs['nom'] = $champsRecup['nom'];
            $champs['prenom'] = $champsRecup['prenom'];
            $champs['tel'] = $champsRecup['tel'];
            $champs['numero_devis'] = $champsRecup['numero_devis'];
            $champs['numero_facture'] = $champsRecup['numero_facture'];
            $champs['etat_paiement'] = $champsRecup['etat_paiement'];
            $champs['date_paiement'] = $champsRecup['date_paiement'];
            $champs['devise_facture'] = $champsRecup['devise_facture'];

            $champsRecup = $comptaFact->obtenir_details($id);

            $i = 1;
            foreach ($champsRecup as $row) {
                $champs['id' . $i] = $row['id'];
                $champs['ref' . $i] = $row['ref'];
                $champs['designation' . $i] = $row['designation'];
                $champs['quantite' . $i] = $row['quantite'];
                $champs['pu' . $i] = $row['pu'];
                $i++;
            }

            $form->setDefaults($champs);
            $form->addElement('hidden', 'id', $id);
        }

        //detail devis
        $form->addElement('header', '', 'Détail Devis');

        if ($action === self::ACTION_EDIT) {
            $form->addElement('date', 'date_facture', 'Date facture', [
                'language' => 'fr',
                'format' => 'd F Y',
                'minYear' => date('Y') - 3,
                'maxYear' => date('Y'),
            ]);
        } else {
            $form->addElement('date', 'date_facture', 'Date facture', [
                'language' => 'fr',
                'format' => 'd F Y',
                'minYear' => date('Y'),
                'maxYear' => date('Y'),
            ]);
        }
        $form->addElement('header', '', 'Facturation');
        $form->addElement('static', 'note', '', 'Ces informations concernent la personne ou la société qui sera facturée<br /><br />');
        $form->addElement('text', 'societe', 'Société', ['size' => 50, 'maxlength' => 100]);
        $form->addElement('text', 'service', 'Service', ['size' => 30, 'maxlength' => 40]);
        $form->addElement('textarea', 'adresse', 'Adresse', ['cols' => 42, 'rows' => 10]);
        $form->addElement('text', 'code_postal', 'Code postal', ['size' => 6, 'maxlength' => 10]);
        $form->addElement('text', 'ville', 'Ville', ['size' => 30, 'maxlength' => 50]);
        $form->addElement('select', 'id_pays', 'Pays', $pays->obtenirPays());

        $form->addElement('header', null, 'Contact');
        $form->addElement('text', 'nom', 'Nom', ['size' => 30, 'maxlength' => 40]);
        $form->addElement('text', 'prenom', 'Prénom', ['size' => 30, 'maxlength' => 40]);
        $form->addElement('text', 'tel', 'Numero de tél', ['size' => 30, 'maxlength' => 40]);
        $form->addElement('text', 'email', 'Email (facture)', ['size' => 30, 'maxlength' => 100]);

        if ($champs['numero_devis'] || $champs['numero_facture']) {
            $form->addElement('header', null, 'Réservé à l\'administration');
            $form->addElement('static', 'note', '', 'Numéro généré automatiquement et affiché en automatique');
            if ($champs['numero_devis']) {
                $form->addElement('text', 'numero_devis', 'Numéro devis', ['size' => 50, 'maxlength' => 100]);
            }
            if ($champs['numero_facture']) {
                $form->addElement('text', 'numero_facture', 'Numéro facture', ['size' => 50, 'maxlength' => 100]);
            }
        } else {
            $form->addElement('hidden', 'numero_devis', 'Numéro devis', ['size' => 50, 'maxlength' => 100]);
            $form->addElement('hidden', 'numero_facture', 'Numéro facture', ['size' => 50, 'maxlength' => 100]);
        }

        $form->addElement('header', null, 'Référence client');
        $form->addElement('static', 'note', '', 'Possible d\'avoir plusieurs références à mettre (obligation client)<br /><br />');
        $form->addElement('text', 'ref_clt1', 'Référence client', ['size' => 50, 'maxlength' => 100]);
        $form->addElement('text', 'ref_clt2', 'Référence client 2', ['size' => 50, 'maxlength' => 100]);
        $form->addElement('text', 'ref_clt3', 'Référence client 3', ['size' => 50, 'maxlength' => 100]);

        $form->addElement('header', '', 'Observation');
        $form->addElement('static', 'note', '', 'Ces informations seront écrites à la fin du document<br /><br />');
        $form->addElement('textarea', 'observation', 'Observation', ['cols' => 42, 'rows' => 5]);

        $form->addElement('header', '', 'Paiement');
        $form->addElement('select', 'devise_facture', 'Monnaie de la facture', [
            'EUR' => 'Euro',
            'DOL' => 'Dollar',
        ], ['size' => 2]);
        $form->addElement('select', 'etat_paiement', 'Etat paiement', ['En attente de paiement', 'Payé', 'Annulé'], ['size' => 3]);
        $form->addElement('date', 'date_paiement', 'Date paiement', [
            'language' => 'fr',
            'format' => 'd F Y',
            'minYear' => date('Y') - 5,
            'maxYear' => date('Y'),
        ]);

        $form->addElement('header', '', 'Contenu');
        $form->addElement('text', 'ref', 'Référence', ['size' => 50, 'maxlength' => 100]);
        $form->addElement('textarea', 'designation', 'Désignation', ['cols' => 42, 'rows' => 5]);
        $form->addElement('text', 'quantite', 'Quantite', ['size' => 50, 'maxlength' => 100]);
        $form->addElement('text', 'pu', 'Prix Unitaire', ['size' => 50, 'maxlength' => 100]);

        for ($i = 1; $i < 6; $i++) {
            $form->addElement('header', '', 'Contenu');
            $form->addElement('static', 'note', '', 'Ligne ' . $i . '<br /><br />');
            $form->addElement('hidden', 'id' . $i, 'id');
            $form->addElement('text', 'ref' . $i, 'Référence', ['size' => 50, 'maxlength' => 100]);
            $form->addElement('textarea', 'designation' . $i, 'Désignation', ['cols' => 42, 'rows' => 5]);
            $form->addElement('text', 'quantite' . $i, 'Quantite', ['size' => 50, 'maxlength' => 100]);
            $form->addElement('text', 'pu' . $i, 'Prix Unitaire', ['size' => 50, 'maxlength' => 100]);
        }

        // boutons
        $form->addElement('header', 'boutons', '');
        $form->addElement('submit', 'soumettre', ucfirst($action));

        // ajoute des regles
        $form->addRule('societe', 'Société manquant', 'required');
        $form->addRule('adresse', 'Adresse manquant', 'required');
        $form->addRule('email', 'Email manquant', 'required');

        if ($form->validate()) {
            $valeur = $form->exportValues();

            $date_ecriture = $valeur['date_facture']['Y'] . '-' . $valeur['date_facture']['F'] . '-' . $valeur['date_facture']['d'];
            $date_paiement = $valeur['date_paiement']['Y'] . '-' . $valeur['date_paiement']['F'] . '-' . $valeur['date_paiement']['d'];

            if ($action === self::ACTION_ADD) {
                // il faut passer obligatoirement par un devis
            } else {
                $ok = $comptaFact->modifier(
                    $id,
                    $date_ecriture,
                    $valeur['societe'],
                    $valeur['service'],
                    $valeur['adresse'],
                    $valeur['code_postal'],
                    $valeur['ville'],
                    $valeur['id_pays'],
                    $valeur['nom'],
                    $valeur['prenom'],
                    $valeur['tel'],
                    $valeur['email'],
                    $valeur['observation'],
                    $valeur['ref_clt1'],
                    $valeur['ref_clt2'],
                    $valeur['ref_clt3'],
                    $valeur['numero_devis'],
                    $valeur['numero_facture'],
                    $valeur['etat_paiement'],
                    $date_paiement,
                    $valeur['devise_facture']
                );
                for ($i = 1; $i < 6; $i++) {
                    $ok = $comptaFact->modifier_details(
                        $valeur['id' . $i],
                        $valeur['ref' . $i],
                        $valeur['designation' . $i],
                        $valeur['quantite' . $i],
                        $valeur['pu' . $i]
                    );
                }
            }

            if ($ok) {
                if ($action === self::ACTION_ADD) {
                    $this->backOfficeLegacyBridge->log('Ajout une écriture ' . $form->exportValue('titre'));
                } else {
                    $this->backOfficeLegacyBridge->log('Modification une écriture ' . $form->exportValue('titre') . ' (' . $id . ')');
                }

                return $this->backOfficeLegacyBridge->afficherMessage('L\'écriture a été ' . ($action === self::ACTION_ADD ? 'ajoutée' : 'modifiée'), $this->urlGenerator->generate('admin_accounting_invoices'));
            }

            if ($this->session instanceof Session) {
                $this->session->getFlashBag()
                    ->add('error', 'Une erreur est survenue lors de ' . ($action === self::ACTION_ADD ? "l'ajout" : 'la modification') . ' de l\'écriture');
            }
        }
        $form = genererFormulaire($form);

        return new Response($this->twig->render('admin/accounting/invoice_edit.html.twig', [
            'action' => $action,
            'formulaire' => $form,
            'factureId' => $factureId,
        ]));
    }
}
