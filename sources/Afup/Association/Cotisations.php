<?php

declare(strict_types=1);

namespace Afup\Site\Association;

use Afup\Site\Corporate\Site;
use Afup\Site\Droits;
use Afup\Site\Utils\Base_De_Donnees;
use Afup\Site\Utils\Mailing;
use Afup\Site\Utils\PDF_Facture;
use Afup\Site\Utils\Utils;
use Afup\Site\Utils\Vat;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Compta\BankAccount\BankAccountFactory;
use AppBundle\Email\Mailer\Attachment;
use AppBundle\Email\Mailer\Mailer;
use AppBundle\Email\Mailer\MailUser;
use AppBundle\Email\Mailer\MailUserFactory;
use AppBundle\Email\Mailer\Message;
use Assert\Assertion;
use DateInterval;
use DateTime;

define('AFUP_COTISATIONS_REGLEMENT_ESPECES', 0);
define('AFUP_COTISATIONS_REGLEMENT_CHEQUE', 1);
define('AFUP_COTISATIONS_REGLEMENT_VIREMENT', 2);
define('AFUP_COTISATIONS_REGLEMENT_AUTRE', 3);
define('AFUP_COTISATIONS_REGLEMENT_ENLIGNE', 4);

define('AFUP_COTISATIONS_PAIEMENT_ERREUR', 0);
define('AFUP_COTISATIONS_PAIEMENT_REGLE', 1);
define('AFUP_COTISATIONS_PAIEMENT_ANNULE', 2);
define('AFUP_COTISATIONS_PAIEMENT_REFUSE', 3);

/**
 * Classe de gestion des personnes morales
 */
class Cotisations
{
    private ?CompanyMemberRepository $companyMemberRepository = null;

    public function __construct(
        private readonly Base_De_Donnees $_bdd,
        private readonly ?Droits $_droits = null,
    ) {
    }

    /**
     * Renvoit la liste des cotisations concernant une personne
     *
     * @param int $type_personne Type de la personne (morale ou physique)
     * @param int $id_personne Identifiant de la personne
     * @param string $champs Champs à renvoyer
     * @param string $ordre Tri des enregistrements
     * @param bool $associatif Renvoyer un tableau associatif ?
     * @return array
     */
    public function obtenirListe($type_personne, $id_personne, string $champs = '*', string $ordre = 'date_fin DESC', bool $associatif = false)
    {
        $requete = 'SELECT';
        $requete .= '  ' . $champs . ' ';
        $requete .= 'FROM';
        $requete .= '  afup_cotisations ';
        $requete .= 'WHERE';
        $requete .= '  type_personne=' . $type_personne;
        $requete .= '  AND id_personne=' . $id_personne . ' ';
        $requete .= 'ORDER BY ' . $ordre;
        if ($associatif) {
            return $this->_bdd->obtenirAssociatif($requete);
        } else {
            return $this->_bdd->obtenirTous($requete);
        }
    }

    /**
     * Renvoit la cotisation demandée
     *
     * @param int $id Identifiant de la cotisation
     * @param string $champs Champs à renvoyer
     * @return array
     */
    public function obtenir($id, string $champs = '*')
    {
        $requete = 'SELECT';
        $requete .= '  ' . $champs . ' ';
        $requete .= 'FROM';
        $requete .= '  afup_cotisations ';
        $requete .= 'WHERE';
        $requete .= '  id=' . $id;
        return $this->_bdd->obtenirEnregistrement($requete);
    }

    /**
     * Renvoit le numéro de la prochaine facture au format : {année}-{index depuis le début de l'année}
     *
     */
    public function _genererNumeroFacture(): string
    {
        $requete = 'SELECT';
        $requete .= "  MAX(CAST(SUBSTRING_INDEX(numero_facture, '-', -1) AS UNSIGNED)) + 1 ";
        $requete .= 'FROM';
        $requete .= '  afup_cotisations ';
        $requete .= 'WHERE';
        $requete .= '  LEFT(numero_facture, 4)=' . $this->_bdd->echapper(date('Y'));
        $requete .= '  OR LEFT(numero_facture, 10)=' . $this->_bdd->echapper("COTIS-" . date('Y'));
        $index = $this->_bdd->obtenirUn($requete);
        return 'COTIS-' . date('Y') . '-' . (is_null($index) ? 1 : $index);
    }

    /**
     * Ajoute une cotisation
     *
     * @param int $type_personne Type de la personne (morale ou physique)
     * @param int $id_personne Identifiant de la personne
     * @param float $montant Adresse de la personne
     * @param int $type_reglement Type de règlement (espèces, chèque, virement)
     * @param string $informations_reglement Informations concernant le règlement (numéro de chèque, de virement etc.)
     * @param int $date_debut Date de début de la
     * cotisation
     * @param int $date_fin Date de fin de la cotisation
     * @param string $commentaires Commentaires concernnant la cotisation
     * @param string $referenceClient Reference client à mentionner sur la facture
     * @return bool     Succès de l'ajout
     */
    public function ajouter($type_personne, $id_personne, $montant, $type_reglement,
                     $informations_reglement, $date_debut, $date_fin, $commentaires, $referenceClient = null): bool
    {
        $requete = 'INSERT INTO ';
        $requete .= '  afup_cotisations (type_personne, id_personne, montant, type_reglement , informations_reglement,';
        $requete .= '                    date_debut, date_fin, numero_facture, token, commentaires, reference_client) ';
        $requete .= 'VALUES (';
        $requete .= $type_personne . ',';
        $requete .= $id_personne . ',';
        $requete .= $montant . ',';
        $requete .= $this->_bdd->echapper($type_reglement) . ',';
        $requete .= $this->_bdd->echapper($informations_reglement) . ',';
        $requete .= $date_debut . ',';
        $requete .= $date_fin . ',';
        $requete .= $this->_bdd->echapper($this->_genererNumeroFacture()) . ',';
        $requete .= $this->_bdd->echapper(base64_encode(random_bytes(30))) . ',';
        $requete .= $this->_bdd->echapper($commentaires) . ',';
        $requete .= $this->_bdd->echapper($referenceClient) . ')';
        return $this->_bdd->executer($requete) !== false;
    }

    /**
     * Modifie une cotisation
     *
     * @param int $id Identifiant de la cotisation à modifier
     * @param int $type_personne Type de la personne (morale ou physique)
     * @param int $id_personne Identifiant de la personne
     * @param float $montant Adresse de la personne
     * @param int $type_reglement Type de règlement (espèces, chèque, virement)
     * @param string $informations_reglement Informations concernant le règlement (numéro de chèque, de virement etc.)
     * @param int $date_debut Date de début de la
     * cotisation
     * @param int $date_fin Date de fin de la cotisation
     * @param string $commentaires Commentaires concernnant la cotisation
     * @param string $referenceClient Reference client à mentionner sur la facture
     * @return bool Succès de la modification
     */
    public function modifier($id, $type_personne, $id_personne, $montant, $type_reglement,
                      $informations_reglement, $date_debut, $date_fin, $commentaires, $referenceClient): bool
    {
        $requete = 'UPDATE';
        $requete .= '  afup_cotisations ';
        $requete .= 'SET';
        $requete .= '  type_personne=' . $type_personne . ',';
        $requete .= '  id_personne=' . $id_personne . ',';
        $requete .= '  montant=' . $montant . ',';
        $requete .= '  type_reglement=' . $type_reglement . ',';
        $requete .= '  informations_reglement=' . $this->_bdd->echapper($informations_reglement) . ',';
        $requete .= '  date_debut=' . $date_debut . ',';
        $requete .= '  date_fin=' . $date_fin . ',';
        $requete .= '  commentaires=' . $this->_bdd->echapper($commentaires) . ',';
        $requete .= '  reference_client=' . $this->_bdd->echapper($referenceClient) . ' ';
        $requete .= 'WHERE';
        $requete .= '  id=' . $id;
        return $this->_bdd->executer($requete) !== false;
    }

    public function estDejaReglee($cmd)
    {
        $requete = 'SELECT';
        $requete .= '  1 ';
        $requete .= 'FROM';
        $requete .= '  afup_cotisations ';
        $requete .= 'WHERE';
        $requete .= '  informations_reglement=' . $this->_bdd->echapper($cmd);
        return $this->_bdd->obtenirUn($requete);
    }

    public function notifierReglementEnLigneAuTresorier(string $cmd, string $total, string $autorisation, string $transaction, UserRepository $userRepository): ?bool
    {
        if (str_starts_with($cmd, 'F')) {
            $invoiceNumber = substr($cmd, 1);
            $cotisation = $this->getByInvoice($invoiceNumber);
            $company = $this->companyMemberRepository ? $this->companyMemberRepository->get($cotisation['id_personne']) : null;
            if ($company === null) {
                throw new \RuntimeException(sprintf('Personne morale non trouvée pour "%s"', $cmd));
            }
            $infos = [
                'nom' => $company->getLastName(),
                'prenom' => $company->getFirstName(),
                'email' => $company->getEmail(),
                'id' => $cotisation['id_personne'],
                'type' => AFUP_PERSONNES_MORALES,
            ];
        } else {
            [$ref, $date, $type_personne, $id_personne, $reste] = explode('-', $cmd, 5);
            $user = $userRepository->get($id_personne);
            if ($user === null) {
                throw new \RuntimeException(sprintf('Personne physique non trouvée pour "%s"', $cmd));
            }
            $infos = [
                'nom' => $user->getLastName(),
                'prenom' => $user->getFirstName(),
                'email' => $user->getEmail(),
                'id' => $id_personne,
                'type' => $type_personne,
            ];
        }

        $sujet = "Paiement cotisation AFUP\n";

        $corps = "Bonjour, \n\n";
        $corps .= "Une cotisation annuelle AFUP a été réglée.\n\n";
        $corps .= "Personne : " . $infos['nom'] . " " . $infos['prenom'] . " (" . $infos['email'] . ")\n";
        $corps .= "URL : " . Site::WEB_PATH . "pages/administration/index.php?page=cotisations&type_personne=" . $infos['type'] . "&id_personne=" . $infos['id'] . "\n";
        $corps .= "Commande : " . $cmd . "\n";
        $corps .= "Total : " . $total . "\n";
        $corps .= "Autorisation : " . $autorisation . "\n";
        $corps .= "Transaction : " . $transaction . "\n\n";

        $ok = Mailing::envoyerMail(new Message($sujet, new MailUser(MailUser::DEFAULT_SENDER_EMAIL, MailUser::DEFAULT_SENDER_NAME), MailUserFactory::tresorier()), $corps);

        if (false === $ok) {
            return false;
        }
        return null;
    }

    public function validerReglementEnLigne($cmd, $total, string $autorisation, string $transaction)
    {
        $reference = substr((string) $cmd, 0, strlen((string) $cmd) - 4);
        $verif = substr((string) $cmd, strlen((string) $cmd) - 3, strlen((string) $cmd));
        $result = false;

        if (str_starts_with((string) $cmd, 'F')) {
            // This is an invoice ==> we dont have to create a new cotisation, just update the existing one
            $invoiceNumber = substr((string) $cmd, 1);
            $cotisation = $this->getByInvoice($invoiceNumber);

            $this
                ->updatePayment(
                    $cotisation['id'],
                    AFUP_COTISATIONS_REGLEMENT_ENLIGNE, "autorisation : " . $autorisation . " / transaction : " . $transaction
                );
        } elseif (substr(md5($reference), -3) === strtolower($verif) && !$this->estDejaReglee($cmd)) {
            [$ref, $date, $type_personne, $id_personne, $reste] = explode('-', (string) $cmd, 5);
            $date_debut = mktime(0, 0, 0, (int) substr($date, 2, 2), (int) substr($date, 0, 2), (int) substr($date, 4, 4));

            $cotisation = $this->obtenirDerniere($type_personne, $id_personne);
            $date_fin_precedente = $cotisation === false ? 0 : $cotisation['date_fin'];

            if ($date_fin_precedente > 0) {
                $date_debut = strtotime('+1day', (int) $date_fin_precedente);
            }

            $date_fin = $this->finProchaineCotisation($cotisation)->format('U');
            $result = $this->ajouter($type_personne,
                $id_personne,
                $total,
                AFUP_COTISATIONS_REGLEMENT_ENLIGNE,
                $cmd,
                $date_debut,
                $date_fin,
                "autorisation : " . $autorisation . " / transaction : " . $transaction);
        }

        return $result;
    }

    /**
     * @return int[]|string[]
     */
    public function getAccountFromCmd($cmd): array
    {
        $arr = explode('-', (string) $cmd, 5);
        // Personne morale : $cmd=FCOTIS-2023-202
        if (3 === count($arr)) {
            return ['type' => UserRepository::USER_TYPE_COMPANY, 'id' => $arr[2]];
        }

        // Personne physique : $cmd=C2023-211120232237-0-5-PAUL-431
        [$ref, $date, $memberType, $memberId, $stuff] = $arr;

        return ['type' => $memberType, 'id' => $memberId];
    }

    /**
     * Supprime une cotisation
     *
     * @param int $id Identifiant de la cotisation à supprimer
     * @return bool Succès de la suppression
     */
    public function supprimer($id)
    {
        $requete = 'DELETE FROM afup_cotisations WHERE id=' . $id;
        return $this->_bdd->executer($requete);
    }

    /**
     * Génère une facture au format PDF
     *
     * @param int $id_cotisation Identifiant de la cotisation
     * @param string $chemin Chemin du fichier PDF à générer. Si ce chemin est omi, le PDF est renvoyé au navigateur.
     * @return int Le numero de la facture
     */
    public function genererFacture($id_cotisation, $chemin = null)
    {
        $requete = 'SELECT * FROM afup_cotisations WHERE id=' . $id_cotisation;
        $cotisation = $this->_bdd->obtenirEnregistrement($requete);

        $table = $cotisation['type_personne'] == AFUP_PERSONNES_MORALES ? 'afup_personnes_morales' : 'afup_personnes_physiques';
        $requete = 'SELECT * FROM ' . $table . ' WHERE id=' . $cotisation['id_personne'];
        $personne = $this->_bdd->obtenirEnregistrement($requete);


        $configuration = $GLOBALS['AFUP_CONF'];

        $dateCotisation = \DateTimeImmutable::createFromFormat('U', $cotisation['date_debut']);
        $bankAccountFactory = new BankAccountFactory();
        $isSubjectedToVat = Vat::isSubjectedToVat($dateCotisation);
        // Construction du PDF
        $pdf = new PDF_Facture($configuration, $bankAccountFactory->createApplyableAt($dateCotisation), $isSubjectedToVat);
        $pdf->AddPage();

        $pdf->Cell(130, 5);
        $pdf->Cell(60, 5, 'Le ' . $dateCotisation->format('d/m/Y'));

        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();

        // A l'attention du client [adresse]
        $pdf->SetFont('Arial', 'BU', 10);
        $pdf->Cell(130, 5, 'Objet : Facture n°' . $cotisation['numero_facture']);
        $pdf->SetFont('Arial', '', 10);

        if ($cotisation['type_personne'] == AFUP_PERSONNES_MORALES) {
            $nom = $personne['raison_sociale'];
            $patternPrefix = $personne['raison_sociale'];
        } else {
            $nom = $personne['prenom'] . ' ' . $personne['nom'];
            $patternPrefix = $personne['nom'];
        }
        $pdf->Ln(10);
        $pdf->MultiCell(130, 5, $nom . "\n" . $personne['adresse'] . "\n" . $personne['code_postal'] . "\n" . $personne['ville']);

        if (isset($cotisation['reference_client'])) {
            $pdf->Ln(10);
            $pdf->MultiCell(180, 5, sprintf(
                "Référence client : %s",
                $cotisation['reference_client']
            ));
        }

        $pdf->Ln(15);

        $pdf->MultiCell(180, 5, "Facture concernant votre adhésion à l'Association Française des Utilisateurs de PHP (AFUP).");

        if (false === $isSubjectedToVat) {
            // Cadre
            $pdf->Ln(10);
            $pdf->SetFillColor(200, 200, 200);
            $pdf->Cell(50, 5, 'Code', 1, 0, 'L', 1);
            $pdf->Cell(100, 5, 'Désignation', 1, 0, 'L', 1);
            $pdf->Cell(40, 5, 'Prix', 1, 0, 'L', 1);

            $pdf->Ln();
            $pdf->SetFillColor(255, 255, 255);
            $pdf->Cell(50, 5, 'ADH', 1);
            $pdf->Cell(100, 5, "Adhésion AFUP jusqu'au " . date('d/m/Y', (int) $cotisation['date_fin']), 1);
            $pdf->Cell(40, 5, $cotisation['montant'] . ' €', 1);

            $pdf->Ln(15);
            $pdf->Cell(10, 5, 'TVA non applicable - art. 293B du CGI');
        } else {
            // On stocke le montant de la cotisation TTC, pour les personnes physiques c'est le même, par contre pour les personnes morales
            // ce n'est pas le même, afin d'éviter d'appliquer deux fois la TVA, on applique ce hotfix
            if ($cotisation['type_personne'] == AFUP_PERSONNES_MORALES) {
                $cotisation['montant'] = Vat::getRoundedWithoutVatPriceFromPriceWithVat($cotisation['montant'], Utils::MEMBERSHIP_FEE_VAT_RATE);
            }


            // Cadre
            $pdf->Ln(10);
            $pdf->SetFillColor(200, 200, 200);
            $pdf->Cell(20, 5, 'Code', 1, 0, 'L', 1);
            $pdf->Cell(95, 5, 'Désignation', 1, 0, 'L', 1);
            $pdf->Cell(25, 5, 'Prix HT', 1, 0, 'R', 1);
            $pdf->Cell(25, 5, 'Taux TVA', 1, 0, 'R', 1);
            $pdf->Cell(25, 5, 'Prix TTC', 1, 0, 'R', 1);

            if ($cotisation['type_personne'] == AFUP_PERSONNES_MORALES) {
                [$totalHt, $total] = $this->buildDetailsPersonneMorale($pdf, $cotisation['montant'], $cotisation['date_fin']);
            } else {
                [$totalHt, $total] = $this->buildDetailsPersonnePhysique($pdf, $cotisation['montant'], $cotisation['date_fin']);
            }

            $pdf->Ln();
            $pdf->SetFillColor(225, 225, 225);
            $pdf->Cell(165, 5, 'Total HT', 1, 0, 'R', 1);
            $pdf->Cell(25, 5, $this->formatFactureValue($totalHt) . ' €', 1, 0, 'R', 1);

            $pdf->Ln();
            $pdf->SetFillColor(255, 255, 255);
            $pdf->Cell(165, 5, 'Total TVA 20%', 1, 0, 'R', 1);
            $pdf->Cell(25, 5, $this->formatFactureValue($total - $totalHt) . ' €', 1, 0, 'R', 1);

            $pdf->Ln();
            $pdf->SetFillColor(225, 225, 225);
            $pdf->Cell(165, 5, 'Total TTC', 1, 0, 'R', 1);
            $pdf->Cell(25, 5, $this->formatFactureValue($total) . ' €', 1, 0, 'R', 1);
        }

        $pdf->Ln(15);
        $pdf->Cell(10, 5, 'Lors de votre règlement, merci de préciser la mention : "Facture n°' . $cotisation['numero_facture'] . '"');

        if (is_null($chemin)) {
            $pattern = str_replace(' ', '', $patternPrefix) . '_' . $cotisation['numero_facture'] . '_' . date('dmY', (int) $cotisation['date_debut']) . '.pdf';

            $pdf->Output($pattern, 'D', true);
        } else {
            $pdf->Output($chemin, 'F', true);
        }

        return $cotisation['numero_facture'];
    }

    private function buildDetailsPersonneMorale(PDF_Facture $pdf, $montant, $dateFin): array
    {
        $montantTtc = $montant * (1 + Utils::MEMBERSHIP_FEE_VAT_RATE);
        $pdf->Ln();
        $pdf->SetFillColor(255, 255, 255);
        $pdf->Cell(20, 5, 'ADH', 1);
        $pdf->Cell(95, 5, "Adhésion AFUP jusqu'au " . date('d/m/Y', (int) $dateFin), 1);
        $pdf->Cell(25, 5, $this->formatFactureValue($montant) . ' €', 1, 0, 'R');
        $pdf->Cell(25, 5, (Utils::MEMBERSHIP_FEE_VAT_RATE * 100 . ' %'), 1, 0, 'R');
        $pdf->Cell(25, 5, $this->formatFactureValue($montantTtc) . ' €', 1, 0, 'R');
        $totalHt = $montant;
        $total = $montantTtc;

        return [$totalHt, $total];
    }

    private function buildDetailsPersonnePhysique(PDF_Facture $pdf, $montant, $dateFin): array
    {
        $montantFixeHt = 5 / 100 * $montant;
        $montantFixeTTc = $montantFixeHt * (1 + Utils::MEMBERSHIP_FEE_VAT_RATE);
        $montantVariable = $montant - $montantFixeTTc;

        $pdf->Ln();
        $pdf->SetFillColor(255, 255, 255);
        $pdf->Cell(20, 5, 'ADH-var', 1);
        $pdf->Cell(95, 5, "Adhésion AFUP jusqu'au " . date('d/m/Y', (int) $dateFin) . ' - part variable', 1);
        $pdf->Cell(25, 5, $this->formatFactureValue($montantFixeHt) . ' €', 1, 0, 'R');
        $pdf->Cell(25, 5, (Utils::MEMBERSHIP_FEE_VAT_RATE * 100 . ' %'), 1, 0, 'R');
        $pdf->Cell(25, 5, $this->formatFactureValue($montantFixeTTc) . ' €', 1, 0, 'R');

        $pdf->Ln();
        $pdf->SetFillColor(255, 255, 255);
        $pdf->Cell(20, 5, 'ADH-fixe', 1);
        $pdf->Cell(95, 5, "Adhésion AFUP jusqu'au " . date('d/m/Y', (int) $dateFin) . ' - part fixe', 1);
        $pdf->Cell(25, 5, $this->formatFactureValue($montantVariable) . ' €', 1, 0, 'R');
        $pdf->Cell(25, 5, '0 %', 1, 0, 'R');
        $pdf->Cell(25, 5, $this->formatFactureValue($montantVariable) . ' €', 1, 0, 'R');

        $totalHt = $montantFixeHt + $montantVariable;
        $total = $montantFixeTTc + $montantVariable;

        return [$totalHt, $total];
    }

    private function formatFactureValue($value): string
    {
        return number_format($value, 2, ',', ' ');
    }

    /**
     * Envoi par mail d'une facture au format PDF
     *
     * @param   int $id_cotisation Identifiant de la cotisation
     * @return bool Succès de l'envoi
     */
    public function envoyerFacture($id_cotisation, Mailer $mailer, UserRepository $userRepository)
    {
        $personne = $this->obtenir($id_cotisation, 'type_personne, id_personne');

        if ($personne['type_personne'] == AFUP_PERSONNES_MORALES) {
            $company = $this->companyMemberRepository ? $this->companyMemberRepository->get($personne['id_personne']) : null;
            Assertion::notNull($company);
            $contactPhysique = [
                'nom'=> $company->getLastName(),
                'prenom'=> $company->getFirstName(),
                'email'=> $company->getEmail(),
            ];
        } else {
            $user = $userRepository->get($personne['id_personne']);
            Assertion::notNull($user);
            $contactPhysique = [
                'nom'=> $user->getLastName(),
                'prenom'=> $user->getFirstName(),
                'email'=> $user->getEmail(),
            ];
        }
        $patternPrefix = $contactPhysique['nom'];

        $corps = "Bonjour,<br />";
        $corps .= "<p>Veuillez trouver ci-joint la facture correspondant à votre adhésion à l'AFUP.</p>";
        $corps .= "<p>Nous restons à votre disposition pour toute demande complémentaire.</p>";
        $corps .= "<p>Le bureau</p>";
        $corps .= AFUP_RAISON_SOCIALE . "<br />";
        $corps .= AFUP_ADRESSE . "<br />";
        $corps .= AFUP_CODE_POSTAL . " " . AFUP_VILLE . "<br />";

        $cheminFacture = AFUP_CHEMIN_RACINE . 'cache/fact' . $id_cotisation . '.pdf';
        $numeroFacture = $this->genererFacture($id_cotisation, $cheminFacture);
        $cotisation = $this->obtenirDerniere($personne['type_personne'], $personne['id_personne']);
        $pattern = str_replace(' ', '', $patternPrefix) . '_' . $numeroFacture . '_' . date('dmY', $cotisation['date_debut']) . '.pdf';

        $message = new Message('Facture AFUP', null, new MailUser(
            $contactPhysique['email'],
            sprintf('%s %s', $contactPhysique['prenom'], $contactPhysique['nom'])
        ));
        $message->addAttachment(new Attachment(
            $cheminFacture,
            $pattern,
            'base64',
            'application/pdf'
        ));
        $ok = $mailer->sendTransactional($message, $corps);
        @unlink($cheminFacture);

        return $ok;
    }

    /**
     * Retourne la dernière cotisation d'une personne morale
     * @param int|string $type_personne
     * @param int $id_personne Identifiant de la personne
     * @return array|false
     */
    public function obtenirDerniere($type_personne, $id_personne)
    {
        $requete = 'SELECT';
        $requete .= '  * ';
        $requete .= 'FROM';
        $requete .= '  afup_cotisations ';
        $requete .= 'WHERE';
        $requete .= '  type_personne=' . $type_personne . ' ';
        $requete .= '  AND id_personne=' . $id_personne . ' ';
        $requete .= 'ORDER BY';
        $requete .= '  date_fin DESC ';
        $requete .= 'LIMIT 0, 1 ';
        return $this->_bdd->obtenirEnregistrement($requete);
    }

    /**
     * Retourne la date de début d'une cotisation.
     *
     * Cette date est déterminée par la date de fin de la cotisation précédente
     * s'il y en a une ou alors sur la date du jour dans le cas contraire.
     *
     * @param    int $type_personne Identifiant du type de personne
     * @param    int $id_personne Identifiant de la personne
     * @return    int                    Timestamp de la date de la cotisation
     */
    public function obtenirDateDebut($type_personne, $id_personne)
    {
        $requete = 'SELECT';
        $requete .= '  date_fin ';
        $requete .= 'FROM';
        $requete .= '  afup_cotisations ';
        $requete .= 'WHERE';
        $requete .= '  type_personne=' . $type_personne . ' ';
        $requete .= 'AND';
        $requete .= ' id_personne=' . $id_personne . ' ';
        $requete .= 'ORDER BY';
        $requete .= ' date_fin DESC';
        $date_debut = $this->_bdd->obtenirUn($requete);

        if ($date_debut !== false) {
            return (int) $date_debut;
        } else {
            return time();
        }
    }

    /**
     * @param array|false $cotisation from Afup_Personnes_Physiques::obtenirDerniereCotisation
     * @return DateTime Date of end of next subscription
     */
    public function finProchaineCotisation($cotisation = false): DateTime
    {
        $endSubscription = $cotisation === false ? new DateTime() : new \DateTime('@' . $cotisation['date_fin']);
        $base = $now = new DateTime();

        $year = new DateInterval('P1Y');

        if ($endSubscription > $now) {
            $base = $endSubscription;
        }

        $base->add($year);
        return $base;
    }

    /**
     * Renvoit la cotisation demandée
     *
     * @param string $invoiceId Identifiant de la facture
     * @param string|null $token Token de la facture. Si null, pas de vérification
     * @return array
     */
    public function getByInvoice(string $invoiceId, string $token = null)
    {
        $requete = 'SELECT';
        $requete .= '  * ';
        $requete .= 'FROM';
        $requete .= '  afup_cotisations ';
        $requete .= 'WHERE';
        $requete .= '  numero_facture = ' . $this->_bdd->echapper($invoiceId);
        if ($token !== null) {
            $requete .= ' AND token = ' . $this->_bdd->echapper($token);
        }

        return $this->_bdd->obtenirEnregistrement($requete);
    }

    /**
     * Modifie une cotisation
     *
     * @param int $id Identifiant de la cotisation à modifier
     * @param int $type_reglement Type de règlement (espèces, chèque, virement)
     * @param string $informations_reglement Informations concernant le règlement (numéro de chèque, de virement etc.)
     * @return bool Succès de la modification
     */
    public function updatePayment($id, $type_reglement, string $informations_reglement): bool
    {
        $requete = 'UPDATE';
        $requete .= '  afup_cotisations ';
        $requete .= 'SET';
        $requete .= '  type_reglement=' . $type_reglement . ',';
        $requete .= '  informations_reglement=' . $this->_bdd->echapper($informations_reglement);
        $requete .= ' WHERE';
        $requete .= '  id=' . $id;
        return $this->_bdd->executer($requete) !== false;
    }

    public function isCurrentUserAllowedToReadInvoice(string $invoiceId)
    {
        if (!$this->_droits) {
            throw new \RuntimeException('La variable $_droits ne doit pas être null.');
        }

        $sql = 'SELECT type_personne, id_personne FROM afup_cotisations WHERE id = ' . $this->_bdd->echapper($invoiceId);
        $result = $this->_bdd->obtenirEnregistrement($sql);

        if (!$result) {
            return false;
        }

        /**
         * si type_personne = 0, alors personne physique: id_personne doit être identique l'id de l'utilisateur connecté
         */
        if ($result['type_personne'] === "0") {
            return $result['id_personne'] == $this->_droits->obtenirIdentifiant();
        }

        /**
         * si type_personne = 1, alors personne morale: id_personne doit être égale à compagnyId de l'utilisateur connecté
         * qui doit aussi avoir le droit "ROLE_COMPAGNY_MANAGER"
         */
        if ($result['type_personne'] == AFUP_PERSONNES_MORALES) {
            return $this->_droits->verifierDroitManagerPersonneMorale($result['id_personne']);
        }

        return false;
    }

    public function setCompanyMemberRepository(CompanyMemberRepository $companyMemberRepository): void
    {
        $this->companyMemberRepository = $companyMemberRepository;
    }
}
