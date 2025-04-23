<?php

declare(strict_types=1);

namespace Afup\Site\Forum;

use Afup\Site\Utils\Mail;
use Afup\Site\Utils\Pays;
use Afup\Site\Utils\PDF_Facture;
use Afup\Site\Utils\Utils;
use Afup\Site\Utils\Vat;
use AppBundle\Compta\BankAccount\BankAccountFactory;
use AppBundle\Email\Mailer\Attachment;
use AppBundle\Email\Mailer\MailUser;
use AppBundle\Email\Mailer\MailUserFactory;
use AppBundle\Email\Mailer\Message;
use AppBundle\Event\Model\Ticket;

class Facturation
{
    /**
     * Instance de la couche d'abstraction à la base de données
     * @var     object
     */
    private $_bdd;

    /**
     * Constructeur.
     *
     * @param  object $bdd Instance de la couche d'abstraction à la base de données
     * @return void
     */
    public function __construct(&$bdd)
    {
        $this->_bdd = $bdd;
    }

    /**
     * Renvoit les informations concernant une inscription
     *
     * @param  string $reference Reference de la facturation
     * @param  string $champs Champs à renvoyer
     * @return array
     */
    public function obtenir($reference, string $champs = '*')
    {
        $requete = 'SELECT';
        $requete .= '  ' . $champs . ' ';
        $requete .= 'FROM';
        $requete .= '  afup_facturation_forum ';
        $requete .= 'WHERE reference=' . $this->_bdd->echapper($reference);
        return $this->_bdd->obtenirEnregistrement($requete);
    }

    /**
     * Renvoit la liste des inscriptions à facturer ou facturé au forum
     *
     * @param  int $id_forum Id du forum
     * @param  string $champs Champs à renvoyer
     * @param  string $ordre Tri des enregistrements
     * @param  bool $associatif Renvoyer un tableau associatif ?
     * @return array
     */
    public function obtenirListe($id_forum = null,
                          string $champs = '*',
                          string $ordre = 'date_reglement',
                          $associatif = false,
                          $filtre = false)
    {
        $requete = 'SELECT';
        $requete .= '  ' . $champs . ' ';
        $requete .= 'FROM';
        $requete .= '  afup_facturation_forum ';
        $requete .= 'WHERE etat IN ( ' . AFUP_FORUM_ETAT_REGLE . ', ' . AFUP_FORUM_ETAT_ATTENTE_REGLEMENT . ', ' . AFUP_FORUM_ETAT_CONFIRME . ') ';
        $requete .= '  AND id_forum =' . $id_forum . ' ';
        if ($filtre) {
            $requete .= '  AND (societe LIKE \'%' . $filtre . '%\' OR reference LIKE \'%' . $filtre . '%\' ) ';
        }
        $requete .= 'ORDER BY ' . $ordre;

        if ($associatif) {
            return $this->_bdd->obtenirAssociatif($requete);
        } else {
            return $this->_bdd->obtenirTous($requete);
        }
    }

    public function creerReference($id_forum, $label): string
    {
        $label = preg_replace('/[^A-Z0-9_\-\:\.;]/', '', strtoupper(supprimerAccents($label)));

        return 'F' . date('Y') . sprintf('%02d', $id_forum) . '-' . date('dm') . '-' . substr($label, 0, 5) . '-' . substr(md5(date('r') . $label), -5);
    }

    public function estFacture($reference)
    {
        $facture = $this->obtenir($reference, 'etat, facturation');
        if ($facture['facturation'] == AFUP_FORUM_FACTURE_A_ENVOYER) {
            $requete = 'UPDATE afup_inscription_forum ';
            $requete .= 'SET facturation=' . AFUP_FORUM_FACTURE_ENVOYEE . ' ';
            $requete .= 'WHERE reference=' . $this->_bdd->echapper($reference);
            $this->_bdd->executer($requete);

            $requete = 'UPDATE afup_facturation_forum ';
            $requete .= 'SET facturation=' . AFUP_FORUM_FACTURE_ENVOYEE . ', date_facture= ' . time() . ' ';
            $requete .= 'WHERE reference=' . $this->_bdd->echapper($reference);
            return $this->_bdd->executer($requete);
        }
        return true;
    }

    public function genererDevis(string $reference, $chemin = null): void
    {
        $requete = 'SELECT aff.*, af.titre AS event_name
        FROM afup_facturation_forum aff
        LEFT JOIN afup_forum af ON af.id = aff.id_forum
        WHERE reference=' . $this->_bdd->echapper($reference);
        $facture = $this->_bdd->obtenirEnregistrement($requete);

        $requete = 'SELECT aif.*, aft.pretty_name
        FROM afup_inscription_forum aif
        LEFT JOIN afup_forum_tarif aft ON aft.id = aif.type_inscription
        WHERE reference=' . $this->_bdd->echapper($reference);
        $inscriptions = $this->_bdd->obtenirTous($requete);


        $configuration = $GLOBALS['AFUP_CONF'];

        $pays = new Pays($this->_bdd);

        $dateFacture = isset($facture['date_facture']) && !empty($facture['date_facture'])
            ? new \DateTimeImmutable('@' . $facture['date_facture'])
            : new \DateTimeImmutable();

        $bankAccountFactory = new BankAccountFactory();
        // Construction du PDF
        $pdf = new PDF_Facture($configuration, $bankAccountFactory->createApplyableAt($dateFacture));
        $pdf->AddPage();

        $pdf->Cell(130, 5);
        $pdf->Cell(60, 5, 'Le ' . $dateFacture->format('d/m/Y'));

        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();

        if (empty($facture['societe'])) {
            $facture['societe'] = $facture['nom'] . " " . $facture['prenom'];
        }

        // A l'attention du client [adresse]
        $pdf->SetFont('Arial', 'BU', 10);
        $pdf->Cell(130, 5, 'Objet : Devis n°' . $reference);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Ln(10);
        $pdf->MultiCell(130, 5, $facture['societe'] . "\n" . $facture['adresse'] . "\n" . $facture['code_postal'] . "\n" . $facture['ville'] . "\n" . $pays->obtenirNom($facture['id_pays']));

        $pdf->Ln(15);

        $pdf->MultiCell(180, 5, sprintf("Devis concernant votre participation au %s organisé par l'Association Française des Utilisateurs de PHP (AFUP).", $facture['event_name']));
        // Cadre
        $pdf->Ln(10);
        $pdf->SetFillColor(200, 200, 200);
        $pdf->Cell(50, 5, 'Type', 1, 0, 'L', 1);
        $pdf->Cell(100, 5, 'Personne inscrite', 1, 0, 'L', 1);
        $pdf->Cell(40, 5, 'Prix', 1, 0, 'L', 1);

        $total = 0;
        foreach ($inscriptions as $inscription) {
            $pdf->Ln();
            $pdf->SetFillColor(255, 255, 255);

            $pdf->Cell(50, 5, $this->truncate($inscription['pretty_name'], 27), 1);
            $pdf->Cell(100, 5, $inscription['prenom'] . ' ' . $inscription['nom'], 1);
            $pdf->Cell(40, 5, $inscription['montant'] . ' €', 1);
            $total += $inscription['montant'];
        }

        $pdf->Ln();
        $pdf->SetFillColor(225, 225, 225);
        $pdf->Cell(150, 5, 'TOTAL', 1, 0, 'L', 1);
        $pdf->Cell(40, 5, $total . ' €', 1, 0, 'L', 1);

        $pdf->Ln(15);
        $pdf->Cell(10, 5, 'TVA non applicable - art. 293B du CGI');

        if (is_null($chemin)) {
            $pdf->Output('Devis - ' . ($facture['societe'] ?: $facture['nom'] . '_' . $facture['prenom']) . ' - ' . date('Y-m-d_H-i', (int) $facture['date_facture']) . '.pdf', 'D', true);
        } else {
            $pdf->Output($chemin, 'F', true);
        }
    }

    protected function truncate($value, $length)
    {
        if ($value <= $length) {
            return $value;
        }

        return substr($value, 0, $length) . '...';
    }

    /**
     * Génère une facture au format PDF
     *
     * @param string $reference Reference de la facture
     * @param string $chemin Chemin du fichier PDF à générer. Si ce chemin est omi, le PDF est renvoyé au navigateur.
     */
    public function genererFacture(string $reference, $chemin = null): string
    {
        $type = '';
        $requete = 'SELECT aff.*, af.titre AS event_name, af.has_prices_defined_with_vat as event_has_prices_defined_with_vat
        FROM afup_facturation_forum aff
        LEFT JOIN afup_forum af ON af.id = aff.id_forum
        WHERE reference=' . $this->_bdd->echapper($reference);
        $facture = $this->_bdd->obtenirEnregistrement($requete);

        $requete = 'SELECT aif.*, aft.pretty_name
        FROM afup_inscription_forum aif
        LEFT JOIN afup_forum_tarif aft ON aft.id = aif.type_inscription
        WHERE reference=' . $this->_bdd->echapper($reference);
        $inscriptions = $this->_bdd->obtenirTous($requete);

        $configuration = $GLOBALS['AFUP_CONF'];

        $pays = new Pays($this->_bdd);

        // Construction du PDF

        $dateFacture = isset($facture['date_facture']) && !empty($facture['date_facture'])
            ? new \DateTimeImmutable('@' . $facture['date_facture'])
            : new \DateTimeImmutable();


        $isSubjectedToVat = Vat::isSubjectedToVat($dateFacture);

        $bankAccountFactory = new BankAccountFactory();
        // Construction du PDF
        $pdf = new PDF_Facture($configuration, $bankAccountFactory->createApplyableAt($dateFacture), $isSubjectedToVat);
        $pdf->AddPage();

        $pdf->Cell(130, 5);
        $pdf->Cell(60, 5, 'Le ' . $dateFacture->format('d/m/Y'));

        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();

        if (empty($facture['societe'])) {
            $facture['societe'] = $facture['nom'] . " " . $facture['prenom'];
        }

        // A l'attention du client [adresse]
        $pdf->SetFont('Arial', 'BU', 10);
        $pdf->Cell(130, 5, 'Objet : Facture n°' . $reference);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Ln(10);
        $pdf->MultiCell(130, 5, $facture['societe'] . "\n" . $facture['adresse'] . "\n" . $facture['code_postal'] . "\n" . $facture['ville'] . "\n" . $pays->obtenirNom($facture['id_pays']));

        $pdf->Ln(15);

        $pdf->MultiCell(180, 5, sprintf("Facture concernant votre participation au %s organisé par l'Association Française des Utilisateurs de PHP (AFUP).", $facture['event_name']));

        if ($facture['informations_reglement']) {
            $pdf->Ln(10);
            $pdf->Cell(32, 5, 'Référence client : ');
            $infos = explode("\n", $facture['informations_reglement']);
            foreach ($infos as $info) {
                $pdf->Cell(100, 5, $info);
                $pdf->Ln();
                $pdf->Cell(32, 5);
            }
        }

        // Cadre
        $pdf->Ln(10);
        $pdf->SetFillColor(200, 200, 200);
        $pdf->Cell(50, 5, 'Type', 1, 0, 'L', 1);
        $pdf->Cell(100 - ($isSubjectedToVat ? 35 : 0), 5, 'Personne inscrite', 1, 0, 'L', 1);
        $pdf->Cell($isSubjectedToVat ? 30 : 40, 5, 'Prix' . ($isSubjectedToVat ? ' HT' : ''), 1, 0, $isSubjectedToVat ? 'R' : 'L', 1);
        if ($isSubjectedToVat) {
            $pdf->Cell(15, 5, 'TVA', 1, 0,  'C', 1);
            $pdf->Cell(30, 5, 'Prix TTC', 1, 0, 'R', 1);
        }


        $total = 0;
        $totalHt = 0;
        foreach ($inscriptions as $inscription) {
            $pdf->Ln();
            $pdf->SetFillColor(255, 255, 255);

            if ($facture['event_has_prices_defined_with_vat']) {
                $montantHt = Vat::getRoundedWithoutVatPriceFromPriceWithVat($inscription['montant'], Utils::TICKETING_VAT_RATE);
                $montant = $inscription['montant'];
            } else {
                $montantHt = $inscription['montant'];
                $montant = Vat::getRoundedWithVatPriceFromPriceWithoutVat($inscription['montant'], Utils::TICKETING_VAT_RATE);
            }


            $pdf->Cell(50, 5, $this->truncate($inscription['pretty_name'], 27), 1);
            $pdf->Cell(100 - ($isSubjectedToVat ? 35 : 0), 5, $inscription['prenom'] . ' ' . $inscription['nom'], 1);
            $pdf->Cell(
                $isSubjectedToVat ? 30 : 40, 5,
                $this->formatFactureValue($isSubjectedToVat ? $montantHt : $montant, $isSubjectedToVat) . ' €',
                1,
                0,
                $isSubjectedToVat ? 'R' : ''
            );

            if ($isSubjectedToVat) {
                $pdf->Cell(15, 5, '10%', 1, 0, 'C');
                $pdf->Cell(30, 5, $this->formatFactureValue($montant, $isSubjectedToVat) . ' €', 1, 0, 'R');
            }

            $totalHt += $montantHt;
            $total += $montant;
        }

        if ($facture['type_reglement'] == 1) { // Paiement par chèque
            $pdf->Ln();
            $pdf->Cell(50, 5, 'FRAIS', 1);
            $pdf->Cell(100, 5, 'Paiement par chèque', 1);
            $pdf->Cell(40, 5, '25' . ' €', 1);
            $total += 25;
        }

        $totalLabel = 'TOTAL';
        if ($isSubjectedToVat) {
            $totalLabel .= ' TTC';
        }

        if ($isSubjectedToVat) {
            $pdf->Ln();
            $pdf->SetFillColor(225, 225, 225);
            $pdf->Cell(160, 5, 'Total HT', 1, 0, 'R', 1);
            $pdf->Cell(30, 5, $this->formatFactureValue($totalHt, $isSubjectedToVat) . ' €', 1, 0, 'R', 1);

            $pdf->Ln();
            $pdf->SetFillColor(255, 255, 255);
            $pdf->Cell(160, 5, 'Total TVA 10%', 1, 0, 'R', 1);
            $pdf->Cell(30, 5, $this->formatFactureValue($total - $totalHt, $isSubjectedToVat) . ' €', 1, 0, 'R', 1);
        }

        $pdf->Ln();
        $pdf->SetFillColor(225, 225, 225);
        $pdf->Cell(
            150 + ($isSubjectedToVat ? 10 : 0),
            5,
            $totalLabel,
            1,
            0,
            $isSubjectedToVat ? 'R' : 'L',
            1
        );
        $pdf->Cell(
            40 - ($isSubjectedToVat ? 10 : 0), 5,
            $this->formatFactureValue($total, $isSubjectedToVat) . ' €',
            1,
            0,
            $isSubjectedToVat ? 'R' : 'L',
            1
        );

        $pdf->Ln(15);
        if ($facture['etat'] == 4) {
            switch ($facture['type_reglement']) {
                case 0:
                    $type = 'par CB';
                    break;
                case 1:
                    $type = 'par chèque';
                    break;
                case 2:
                    $type = 'par virement';
                    break;
            }
            $pdf->SetTextColor(255, 0, 0);
            $pdf->Cell(130, 5);
            if ($facture['type_reglement'] != Ticket::PAYMENT_NONE) {
                $pdf->Cell(60, 5, 'Payé ' . $type . ' le ' . date('d/m/Y', (int) $facture['date_reglement']));
            }
            $pdf->SetTextColor(0, 0, 0);
        }
        $pdf->Ln();
        if (false === $isSubjectedToVat) {
            $pdf->Cell(10, 5, 'TVA non applicable - art. 293B du CGI');
        }

        if (is_null($chemin)) {
            $pdf->Output('Facture - ' . ($facture['societe'] ?: $facture['nom'] . '_' . $facture['prenom']) . ' - ' . date('Y-m-d_H-i', (int) $facture['date_facture']) . '.pdf', 'D', true);
        } else {
            $pdf->Output($chemin, 'F', true);
        }

        return $reference;
    }

    private function formatFactureValue($value, bool $isSubjectedToVat)
    {
        if (false === $isSubjectedToVat) {
            return $value;
        }

        return number_format((float) $value, 2, ',', ' ');
    }

    /**
     * Envoi par mail d'une facture au format PDF
     *
     * @param string|array $reference Invoicing reference as string, or the invoice itself
     * @return bool Succès de l'envoi
     */
    public function envoyerFacture($reference, $copyTresorier = true, $facturer = true)
    {
        if (is_array($reference)) {
            $personne = $reference;
            $reference = $personne['reference'];
        } else {
            $personne = $this->obtenir($reference, 'email, nom, prenom');
        }

        $cheminFacture = AFUP_CHEMIN_RACINE . 'cache' . DIRECTORY_SEPARATOR . 'fact' . $reference . '.pdf';
        $numeroFacture = $this->genererFacture($reference, $cheminFacture);

        $message = new Message(
            'Facture évènement AFUP',
            MailUserFactory::afup(),
            new MailUser($personne['email'], sprintf('%s %s', $personne['prenom'], $personne['nom']))
        );
        $mailer = Mail::createMailer();
        $mailer->renderTemplate($message,'mail_templates/facture-forum.html.twig', [
            'raison_sociale' => AFUP_RAISON_SOCIALE,
            'adresse' => AFUP_ADRESSE,
            'ville' => AFUP_CODE_POSTAL . ' ' . AFUP_VILLE,
        ]);
        $message->addAttachment(new Attachment(
            $cheminFacture,
            'facture-' . $numeroFacture . '.pdf',
            'base64',
            'application/pdf'
        ));
        if ($copyTresorier) {
            $message->addBcc(MailUserFactory::tresorier());
        }
        $ok = $mailer->send($message);
        @unlink($cheminFacture);

        if ($ok && $facturer) {
            $this->estFacture($reference);
        }

        return $ok;
    }

    /**
     * Changement de la date de réglement d'une facture
     * @param integer $reference
     * @param integer $date_reglement
     */
    public function changerDateReglement($reference, $date_reglement)
    {
        $requete = 'UPDATE ';
        $requete .= '  afup_facturation_forum ';
        $requete .= 'SET ';
        $requete .= '  date_reglement = ' . intval($date_reglement) . ' ';
        $requete .= 'WHERE';
        $requete .= '  reference=' . $this->_bdd->echapper($reference);
        return $this->_bdd->executer($requete);
    }
}
