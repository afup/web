<?php

declare(strict_types=1);

// Impossible to access the file itself
use Afup\Site\Forum\Facturation;
use Afup\Site\Forum\Forum;
use Afup\Site\Forum\Inscriptions;
use Afup\Site\Utils\Logs;
use Afup\Site\Utils\Pays;
use AppBundle\Controller\LegacyController;
use AppBundle\Event\Model\Invoice;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\TicketEventTypeRepository;
use AppBundle\Event\Model\Ticket;
use AppBundle\Event\Ticket\TicketTypeAvailability;

/** @var LegacyController $this */
if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$action = verifierAction(['lister', 'ajouter', 'modifier', 'supprimer','envoyer_convocation', 'generer_mail_inscription_afup', 'generer_inscription_afup']);
$tris_valides = ['i.date', 'i.nom', 'f.societe', 'i.etat'];
$sens_valides = [ 'desc','asc' ];
$smarty->assign('action', $action);

$eventRepository = $this->eventRepository;
$ticketEventTypeRepository = $this->ticketEventTypeRepository;
$ticketTypeAvailability = $this->ticketTypeAvailability;
$invoiceService = $this->invoiceService;
$invoiceRepository = $this->invoiceRepository;
$session = $this->requestStack->getSession();
$urlGenerator = $this->urlGenerator;
$eventStatsRepository = $this->eventStatsRepository;
$ticketRepository = $this->ticketRepository;

function updateGlobalsForTarif(
    EventRepository $eventRepository,
    TicketEventTypeRepository $ticketEventTypeRepository,
    TicketTypeAvailability $ticketTypeAvailability,
    $forumId,
    &$membersTickets = []
): array {
    global $AFUP_Tarifs_Forum, $AFUP_Tarifs_Forum_Lib;
    $event = $eventRepository->get($forumId);
    $ticketTypes = $ticketEventTypeRepository->getTicketsByEvent($event, false);
    $AFUP_Tarifs_Forum_Restantes = [];

    foreach ($ticketTypes as $ticketType) {
        /**
         * @var $ticketType \AppBundle\Event\Model\TicketEventType
         */
        $AFUP_Tarifs_Forum[$ticketType->getTicketTypeId()] = $ticketType->getPrice();
        $AFUP_Tarifs_Forum_Lib[$ticketType->getTicketTypeId()] = $ticketType->getTicketType()->getPrettyName();
        $AFUP_Tarifs_Forum_Restantes[$ticketType->getTicketTypeId()] = $ticketTypeAvailability->getStock($ticketType, $event);

        if ($ticketType->getTicketType()->getIsRestrictedToMembers()) {
            $membersTickets[] = $ticketType->getTicketTypeId();
        }
    }

    return ['restantes' => $AFUP_Tarifs_Forum_Restantes];
}



$forum = new Forum($bdd);
$forum_inscriptions = new Inscriptions($bdd);
$forum_facturation = new Facturation($bdd);

if ($action == 'lister') {
    $list_champs = 'i.id, i.date, i.nom, i.prenom, i.email, f.societe, i.etat, i.coupon, i.type_inscription, f.type_reglement, i.presence_day1, i.presence_day2';
    $list_ordre = 'date desc';
    $list_sens = 'desc';
    $list_associatif = false;
    $list_filtre = false;

    if (isset($_GET['tri']) && in_array($_GET['tri'], $tris_valides)
        && isset($_GET['sens']) && in_array($_GET['sens'], $sens_valides)) {
        $list_ordre = $_GET['tri'] . ' ' . $_GET['sens'];
    }
    if (isset($_GET['filtre'])) {
        $list_filtre = $_GET['filtre'];
    }

    if (!isset($_GET['id_forum']) || intval($_GET['id_forum']) == 0) {
        $_GET['id_forum'] = $forum->obtenirDernier();
    }
    $forumData = $forum->obtenir($_GET['id_forum']);
    $smarty->assign('id_forum', $_GET['id_forum']);
    $memberTickets = [];

    $retour = updateGlobalsForTarif($eventRepository, $ticketEventTypeRepository, $ticketTypeAvailability, $_GET['id_forum'], $memberTickets);
    $restantes = $retour['restantes'];

    $smarty->assign('forum_tarifs_members', $memberTickets);
    $smarty->assign('forum_tarifs_lib',$AFUP_Tarifs_Forum_Lib);
    $smarty->assign('forum_tarifs_restantes', $restantes);
    $smarty->assign('forum_tarifs',$AFUP_Tarifs_Forum);
    $stats = $eventStatsRepository->getStats($_GET['id_forum']);
    $smarty->assign('statistiques', [
        'premier_jour' => [
            'inscrits' => $stats->firstDay->registered,
            'confirmes' => $stats->firstDay->confirmed,
            'en_attente_de_reglement' => $stats->firstDay->pending,
        ],
        'second_jour' => [
            'inscrits' => $stats->secondDay->registered,
            'confirmes' => $stats->secondDay->confirmed,
            'en_attente_de_reglement' => $stats->secondDay->pending,
        ],
        'types_inscriptions' => [
            'confirmes' => $stats->ticketType->confirmed,
            'inscrits' => $stats->ticketType->registered,
            'payants' => $stats->ticketType->paying,
        ],
    ]);

    $smarty->assign('forums', $forum->obtenirListActive());
    $smarty->assign('inscriptions', $forum_inscriptions->obtenirListe($_GET['id_forum'], $list_champs, $list_ordre, $list_associatif, $list_filtre));
    $smarty->assign('finForum', (new \DateTime($forumData['date_fin']))->format('U'));
    $smarty->assign('now', (new \DateTime())->format('U'));
} elseif ($action == 'supprimer') {
    /** @var Invoice|null $invoice */
    $invoice = $invoiceRepository->getByReference($_GET['id']);
    if ($forum_inscriptions->supprimerInscription($_GET['id']) && (null === $invoice || $invoiceService->deleteInvoice($invoice))) {
        Logs::log('Suppression de l\'inscription ' . $_GET['id']);
        afficherMessage('L\'inscription a été supprimée', 'index.php?page=forum_inscriptions&action=lister');
    } else {
        afficherMessage('Une erreur est survenue lors de la suppression de l\'inscription', 'index.php?page=forum_inscriptions&action=lister', true);
    }
} elseif ($action == 'generer_mail_inscription_afup') {
    $champs = $forum_inscriptions->obtenir($_GET['id']);
    $champs2 = $forum_facturation->obtenir($champs['reference']);
    $info_forum = $forum->obtenir($champs['id_forum']);
    $texte  = ' - civilité :    ' . $champs['civilite'] . PHP_EOL;
    $texte .= ' - nom :         ' . $champs['nom'] . PHP_EOL;
    $texte .= ' - prénom :      ' . $champs['prenom'] . PHP_EOL;
    $texte .= ' - email :       ' . $champs['email'] . PHP_EOL;
    $texte .= ' - adresse :     ' . $champs2['adresse'] . PHP_EOL;
    $texte .= ' - code postal : ' . $champs2['code_postal'] . PHP_EOL;
    $texte .= ' - ville :       ' . $champs2['ville'] . PHP_EOL;
    $texte .= ' - pays :        ' . $champs2['id_pays'] . PHP_EOL;
    $smarty->assign('texte_mail', $texte);
    $smarty->assign('info_forum', $info_forum);
} elseif ($action == 'generer_inscription_afup') {
    $champs = $forum_inscriptions->obtenir($_GET['id']);
    $champs2 = $forum_facturation->obtenir($champs['reference']);
    $session->set('generer_personne_physique', [
        'civilite' => $champs['civilite'],
        'nom' => $champs['nom'],
        'prenom' => $champs['prenom'],
        'email' => $champs['email'],
        'adresse' => $champs2['adresse'],
        'code_postal' => $champs2['code_postal'],
        'ville' => $champs2['ville'],
        'id_pays' => $champs2['id_pays'],
        'telephone_fixe' => $champs['telephone'],
        'telephone_portable' => $champs['telephone'],
        'etat' => 1,
    ]);
    afficherMessage("L'inscription a été pré-remplie\nPensez à générer le login",  $urlGenerator->generate('admin_members_add'));
} else {
    $pays = new Pays($bdd);

    $formulaire = instancierFormulaire();
    if ($action == 'ajouter') {
        $formulaire->setDefaults(
            [
                'civilite' => 'M.',
                'id_pays_facturation' => 'FR',
                'type_inscription' => -1,
                'type_reglement' => -1,
                'citer_societe' => 1,
                'mail_partenaire' => 0,
                'newsletter_afup' => 0,
                'newsletter_nexen' => 0,
                'date_reglement' => (new \DateTime())->format('Y-m-d')
            ]
        );
    } else {
        $champs = $forum_inscriptions->obtenir($_GET['id']);
        if ($champs == false) {
            afficherMessage('L\'inscription n\'existe plus', 'index.php?page=forum_inscriptions&action=lister');
            exit(0);
        }
        $champs2 = $forum_facturation->obtenir($champs['reference']);
        $champs['type_reglement']          = $champs2['type_reglement'];
        $champs['informations_reglement']  = $champs2['informations_reglement'];
        $champs['date_reglement']          = $champs2['date_reglement'];
        $champs['autorisation']            = $champs2['autorisation'];
        $champs['transaction']             = $champs2['transaction'];
        $champs['societe_facturation']     = $champs2['societe'];
        $champs['nom_facturation']         = $champs2['nom'];
        $champs['prenom_facturation']      = $champs2['prenom'];
        $champs['adresse_facturation']     = $champs2['adresse'];
        $champs['code_postal_facturation'] = $champs2['code_postal'];
        $champs['ville_facturation']       = $champs2['ville'];
        $champs['id_pays_facturation']     = $champs2['id_pays'];
        $champs['email_facturation']       = $champs2['email'];

        /** @var Ticket $ticket */
        $ticket = $ticketRepository->get($_GET['id']);
        if (null !== $ticket) {
            $champs['commentaires'] = $ticket->getComments();
        }

        $formulaire->setDefaults($champs);

        if (isset($champs) && isset($champs['id_forum'])) {
            $_GET['id_forum'] = $champs['id_forum'];
        }
    }
    updateGlobalsForTarif($eventRepository, $ticketEventTypeRepository, $ticketTypeAvailability, $_GET['id_forum']);

    $formulaire->addElement('hidden', 'old_reference', (isset($champs) ? $champs['reference'] : ''));
    $formulaire->addElement('hidden', 'id_forum', $_GET['id_forum']);

    $formulaire->addElement('header', null, 'Informations');
    $groupe = [];
    foreach ($AFUP_Tarifs_Forum as $tarif_key => $tarifs) {
        $groupe[] = $formulaire->createElement('radio', 'type_inscription', null, $AFUP_Tarifs_Forum_Lib[$tarif_key] . ' (<strong>' . $AFUP_Tarifs_Forum[$tarif_key] . ' €</strong>)' , $tarif_key);
    }


    $formulaire->addGroup($groupe, 'groupe_type_inscription', 'Formule', '<br />', false);

    $formulaire->addElement('select', 'civilite'                 , 'Civilité'       , ['M.' => 'M.', 'Mme' => 'Mme']);
    $formulaire->addElement('text'  , 'nom'                      , 'Nom'            , ['size' => 30, 'maxlength' => 40]);
    $formulaire->addElement('text'  , 'prenom'                   , 'Prénom'         , ['size' => 30, 'maxlength' => 40]);
    $formulaire->addElement('text'  , 'email'                    , 'Email'          , ['size' => 30, 'maxlength' => 100]);
    $formulaire->addElement('text'  , 'telephone'                , 'Tél.'           , ['size' => 20, 'maxlength' => 20]);

    $groupe = [];

    $formulaire->addElement('header', null          , 'Réservé à l\'administration');
    $formulaire->addElement('static'  , 'note'                   , ''               , 'La reference est utilisée comme numéro de facture. Elle peut être commune à plusieurs inscriptions...<br /><br />');

    if ($action != 'ajouter') {
        $formulaire->addElement('static', 'html', '', '<a href="/pages/administration/index.php?' . http_build_query(['page' => 'forum_facturation', 'id_forum' => $_GET['id_forum'], 'filtre' => $champs['reference']]) . '">Rechercher la facture</a>');
    }


    $formulaire->addElement('text'  , 'reference'   , 'Référence'   , ['size' => 50, 'maxlength' => 100]);
    $formulaire->addElement('text'  , 'autorisation', 'Autorisation', ['size' => 50, 'maxlength' => 100]);
    $formulaire->addElement('text'  , 'transaction' , 'Transaction' , ['size' => 50, 'maxlength' => 100]);

    $state = [AFUP_FORUM_ETAT_CREE          => 'Inscription créée',
        AFUP_FORUM_ETAT_ANNULE            => 'Inscription annulée',
        AFUP_FORUM_ETAT_ERREUR            => 'Paiement CB erreur',
        AFUP_FORUM_ETAT_REFUSE            => 'Paiement CB refusé',
        AFUP_FORUM_ETAT_REGLE             => 'Inscription réglée',
        AFUP_FORUM_ETAT_INVITE            => 'Invitation',
        AFUP_FORUM_ETAT_ATTENTE_REGLEMENT => 'Attente règlement',
        AFUP_FORUM_ETAT_CONFIRME          => 'Inscription confirmée',
        AFUP_FORUM_ETAT_A_POSTERIORI      => 'Inscription à posteriori',
        ];
    $formulaire->addElement('select', 'etat'        , 'Etat'        , $state);

    $facturation = [AFUP_FORUM_FACTURE_A_ENVOYER => 'Facture à envoyer',
        AFUP_FORUM_FACTURE_ENVOYEE                    => 'Facture envoyée',
        AFUP_FORUM_FACTURE_RECUE                      => 'Facture reçue',
        ];
    $formulaire->addElement('select', 'facturation' , 'Facturation'  , $facturation);

    $formulaire->addElement('header'  , ''                       , 'Règlement');
    $groupe = [];
    $groupe[] = $formulaire->createElement('radio', 'type_reglement', null, 'Carte bancaire', AFUP_FORUM_REGLEMENT_CARTE_BANCAIRE);
    $groupe[] = $formulaire->createElement('radio', 'type_reglement', null, 'Chèque'        , AFUP_FORUM_REGLEMENT_CHEQUE);
    $groupe[] = $formulaire->createElement('radio', 'type_reglement', null, 'Virement'      , AFUP_FORUM_REGLEMENT_VIREMENT);
    $groupe[] = $formulaire->createElement('radio', 'type_reglement', null, 'Aucun'         , AFUP_FORUM_REGLEMENT_AUCUN);
    $formulaire->addGroup($groupe, 'groupe_type_reglement', 'Règlement', '&nbsp;', false);
    $formulaire->addElement('textarea'   , 'informations_reglement', 'Informations règlement', ['cols' => 42, 'rows' => 4]);


    $current = $forum->obtenir($_GET['id_forum']);
    $formulaire->addElement('date'    , 'date_reglement'     , 'Date', ['language' => 'fr', 'minYear' => $current['forum_annee']-2, 'maxYear' => $current['forum_annee']+2]);


    $formulaire->addElement('header'  , ''                       , 'Facturation');
    $formulaire->addElement('static'  , 'note'                   , ''               , 'Ces informations concernent la personne ou la société qui sera facturée<br /><br />');
    $formulaire->addElement('text'    , 'societe_facturation'    , 'Société'        , ['size' => 50, 'maxlength' => 100]);
    $formulaire->addElement('text'    , 'nom_facturation'        , 'Nom'            , ['size' => 30, 'maxlength' => 40]);
    $formulaire->addElement('text'    , 'prenom_facturation'     , 'Prénom'         , ['size' => 30, 'maxlength' => 40]);
    $formulaire->addElement('textarea', 'adresse_facturation'    , 'Adresse'        , ['cols' => 42, 'rows'      => 10]);
    $formulaire->addElement('text'    , 'code_postal_facturation', 'Code postal'    , ['size' =>  6, 'maxlength' => 10]);
    $formulaire->addElement('text'    , 'ville_facturation'      , 'Ville'          , ['size' => 30, 'maxlength' => 50]);
    $formulaire->addElement('select'  , 'id_pays_facturation'    , 'Pays'           , $pays->obtenirPays());
    $formulaire->addElement('text'    , 'email_facturation'      , 'Email (facture)', ['size' => 30, 'maxlength' => 100]);
    $formulaire->addElement('text'    , 'coupon'                 , 'Coupon'         , ['size' => 30, 'maxlength' => 200]);

    $formulaire->addElement('header', null, 'Divers');
    $formulaire->addElement('textarea', 'commentaires'           , 'Commentaires', ['cols' => 42, 'rows' => 5]);
    $formulaire->addElement('static', 'label', null, "J'accepte que ma compagnie soit citée comme participant à la conférence");
    $groupe = [];
    $groupe[] = $formulaire->createElement('radio', 'citer_societe', null, 'oui', 1);
    $groupe[] = $formulaire->createElement('radio', 'citer_societe', null, 'non', 0);
    $formulaire->addGroup($groupe, 'groupe_citer_societe', null, '&nbsp;', false);
    $formulaire->addElement('static', 'label', null, "Je souhaite être tenu au courant des rencontres de l'AFUP sur des sujets afférents à PHP");
    $groupe = [];
    $groupe[] = $formulaire->createElement('radio', 'newsletter_afup', null, 'oui', 1);
    $groupe[] = $formulaire->createElement('radio', 'newsletter_afup', null, 'non', 0);
    $formulaire->addGroup($groupe, 'groupe_newsletter_afup', null, '&nbsp;', false);
    $formulaire->addElement('static', 'label', null, "Je souhaite être tenu au courant de l'actualité PHP via la newsletter de notre sponsor");
    $groupe = [];
    $groupe[] = $formulaire->createElement('radio', 'newsletter_nexen', null, 'oui', 1);
    $groupe[] = $formulaire->createElement('radio', 'newsletter_nexen', null, 'non', 0);
    $formulaire->addGroup($groupe, 'groupe_newsletter_nexen', null, '&nbsp;', false);
    $formulaire->addElement('static', 'label', null, "Je souhaite recevoir des informations de la part de vos partenaires presse/media");
    $groupe = [];
    $groupe[] = $formulaire->createElement('radio', 'mail_partenaire', null, 'oui', 1);
    $groupe[] = $formulaire->createElement('radio', 'mail_partenaire', null, 'non', 0);
    $formulaire->addGroup($groupe, 'groupe_mail_partenaire', null, '&nbsp;', false);

    $formulaire->addElement('header', null, 'Transport');
    $formulaire->addElement('select', 'transport_mode', 'Quel est votre mode de transport ?', Ticket::TRANSPORT_MODES);
    $formulaire->addElement('select', 'transport_distance', 'Quelle sera la distance parcourue ?', Ticket::TRANSPORT_DISTANCES);

    $formulaire->addElement('header', 'boutons'  , '');
    $formulaire->addElement('submit', 'soumettre', 'Soumettre');

    // On ajoute les règles
    $formulaire->addGroupRule('groupe_type_inscription', 'Formule non sélectionnée' , 'required', null, 1);
    $formulaire->addGroupRule('groupe_type_reglement'  , 'Règlement non sélectionné', 'required', null, 1);
    $formulaire->addRule('civilite'               , 'Civilité non sélectionnée', 'required');
    $formulaire->addRule('nom'                    , 'Nom manquant'             , 'required');
    $formulaire->addRule('prenom'                 , 'Prénom manquant'          , 'required');
    $formulaire->addRule('email'                  , 'Email manquant'           , 'required');
    $formulaire->addRule('email'                  , 'Email invalide'           , 'email');

    if ($formulaire->validate()) {
        $valeurs = $formulaire->exportValues();

        // Date de réglement au 01/01 => non defini
        if ($valeurs['date_reglement']['d'] == 1 && $valeurs['date_reglement']['M'] == 1) {
            $valeurs['date_reglement'] = null;
        } else {
            $valeurs['date_reglement'] = mktime(0,0,0, (int) $valeurs['date_reglement']['M'], (int) $valeurs['date_reglement']['d'], (int) $valeurs['date_reglement']['Y']);
        }

        if ($action == 'ajouter') {
            // On génére la référence si nécessaire
            if (empty($valeurs['reference'])) {
                $label = (empty($valeurs['societe_facturation']) ? (empty($valeurs['nom_facturation']) ? $valeurs['nom'] : $valeurs['nom_facturation']) : $valeurs['societe_facturation']);
                $valeurs['reference'] = $forum_facturation->creerReference($valeurs['id_forum'], $label);
            }

            // On ajoute l'inscription dans la base de données
            $ticket = new Ticket();
            $ticket->setDate(new DateTime());
            $ticket->setAmount($GLOBALS['AFUP_Tarifs_Forum'][$valeurs['type_inscription']]);
            $ticket->setForumId($valeurs['id_forum']);
            $ticket->setReference($valeurs['reference']);
            $ticket->setTicketTypeId($valeurs['type_inscription']);
            $ticket->setCivility($valeurs['civilite']);
            $ticket->setLastname($valeurs['nom']);
            $ticket->setFirstname($valeurs['prenom']);
            $ticket->setEmail($valeurs['email']);
            $ticket->setPhoneNumber($valeurs['telephone']);
            $ticket->setVoucher($valeurs['coupon']);
            $ticket->setCompanyCitation($valeurs['citer_societe']);
            $ticket->setNewsletter($valeurs['newsletter_afup']);
            $ticket->setOptin((bool) $valeurs['mail_partenaire']);
            $ticket->setComments($valeurs['commentaires']);
            $ticket->setStatus($valeurs['etat']);
            $ticket->setInvoiceStatus($valeurs['facturation']);
            $ticket->setTransportMode($valeurs['transport_mode']);
            $ticket->setTransportDistance($valeurs['transport_distance']);
            try {
                $ticketRepository->save($ticket);
                $ok = true;
            } catch (Exception $e) {
                $ok = false;
            }
        } else {
            $ok = $forum_inscriptions->modifierInscription($_GET['id'],
                                                           $valeurs['reference'],
                                                           $valeurs['type_inscription'],
                                                           $valeurs['civilite'],
                                                           $valeurs['nom'],
                                                           $valeurs['prenom'],
                                                           $valeurs['email'],
                                                           $valeurs['telephone'],
                                                           $valeurs['coupon'],
                                                           $valeurs['citer_societe'],
                                                           $valeurs['newsletter_afup'],
                                                           $valeurs['newsletter_nexen'],
                                                           $valeurs['mail_partenaire'],
                                                           $valeurs['commentaires'],
                                                           $valeurs['etat'],
                                                           $valeurs['facturation'],
                                                           (int) $valeurs['transport_mode'],
                                                           (int) $valeurs['transport_distance']);

            /** @var Ticket $ticket */
            $ticket = $ticketRepository->get($_GET['id']);
            if (null !== $ticket) {
                $ticket->setComments($valeurs['commentaires']);
                $ticketRepository->save($ticket);
            }
        }

        try {
            $paymentDate = null;
            if (is_numeric($valeurs['date_reglement'])) {
                $paymentDate = new DateTime('@' . $valeurs['date_reglement']);
            } else {
                $paymentDate = null !== $valeurs['date_reglement'] ? new DateTime('@' . $valeurs['date_reglement']) : null;
            }
            $invoiceService->handleInvoicing($valeurs['reference'],
                                                    $valeurs['type_reglement'],
                                                    $valeurs['informations_reglement'],
                                                    $paymentDate,
                                                    $valeurs['email_facturation'],
                                                    $valeurs['societe_facturation'],
                                                    $valeurs['nom_facturation'],
                                                    $valeurs['prenom_facturation'],
                                                    $valeurs['adresse_facturation'],
                                                    $valeurs['code_postal_facturation'],
                                                    $valeurs['ville_facturation'],
                                                    $valeurs['id_pays_facturation'],
                                                    $valeurs['id_forum'],
                                                    $valeurs['old_reference'],
                                                    $valeurs['autorisation'],
                                                    $valeurs['transaction'],
                                                    $valeurs['etat']);
        } catch (Exception $e) {
            $ok = false;
        }

        if ($ok) {
            if ($action == 'ajouter') {
                Logs::log('Ajout de l\'inscription de ' . $formulaire->exportValue('prenom') . ' ' . $formulaire->exportValue('nom'));
            } else {
                Logs::log('Modification de l\'inscription de ' . $formulaire->exportValue('prenom') . ' ' . $formulaire->exportValue('nom') . ' (' . $_GET['id'] . ')');
            }
            afficherMessage('L\'inscription a été ' . (($action == 'ajouter') ? 'ajoutée' : 'modifiée'), 'index.php?page=forum_inscriptions&action=lister&id_forum=' . $valeurs['id_forum']);
        } else {
            $smarty->assign('erreur', 'Une erreur est survenue lors de ' . (($action == 'ajouter') ? "l'ajout" : 'la modification') . ' de l\'inscription');
        }
    }

    $current = $forum->obtenir($_GET['id_forum'], 'titre');
    $smarty->assign('forum_name', $current['titre']);
    $smarty->assign('formulaire', genererFormulaire($formulaire));
}
