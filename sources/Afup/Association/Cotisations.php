<?php

declare(strict_types=1);

namespace Afup\Site\Association;

use Afup\Site\Droits;
use Afup\Site\Utils\Base_De_Donnees;
use Afup\Site\Utils\Mailing;
use Afup\Site\Utils\PDF_Facture;
use Afup\Site\Utils\Utils;
use Afup\Site\Utils\Vat;
use AppBundle\Association\MemberType;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Compta\BankAccount\BankAccountFactory;
use AppBundle\Email\Mailer\Attachment;
use AppBundle\Email\Mailer\Mailer;
use AppBundle\Email\Mailer\MailUser;
use AppBundle\Email\Mailer\MailUserFactory;
use AppBundle\Email\Mailer\Message;
use AppBundle\MembershipFee\Model\MembershipFee;
use AppBundle\MembershipFee\Model\Repository\MembershipFeeRepository;
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
    private ?MembershipFeeRepository $membershipFeeRepository = null;

    public function __construct(
        private readonly Base_De_Donnees $_bdd,
        private readonly ?Droits $_droits = null,
    ) {}

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
    public function ajouter(MemberType $type_personne, $id_personne, $montant, $type_reglement,
                     $informations_reglement, $date_debut, $date_fin, $commentaires, $referenceClient = null): bool
    {
        $requete = 'INSERT INTO ';
        $requete .= '  afup_cotisations (type_personne, id_personne, montant, type_reglement , informations_reglement,';
        $requete .= '                    date_debut, date_fin, numero_facture, token, commentaires, reference_client, date_facture) ';
        $requete .= 'VALUES (';
        $requete .= $type_personne->value . ',';
        $requete .= $id_personne . ',';
        $requete .= $montant . ',';
        $requete .= $this->_bdd->echapper($type_reglement) . ',';
        $requete .= $this->_bdd->echapper($informations_reglement) . ',';
        $requete .= $date_debut . ',';
        $requete .= $date_fin . ',';
        $requete .= $this->_bdd->echapper($this->_genererNumeroFacture()) . ',';
        $requete .= $this->_bdd->echapper(base64_encode(random_bytes(30))) . ',';
        $requete .= $this->_bdd->echapper($commentaires) . ',';
        $requete .= $this->_bdd->echapper($referenceClient) . ',';
        $requete .= $this->_bdd->echapper(date('Y-m-d\TH:i:s')) . ')';
        return $this->_bdd->executer($requete) !== false;
    }

    public function isAlreadyPaid($cmd): bool
    {
        return $this->membershipFeeRepository->getOneBy(['paymentDetails' => $cmd]) instanceof MembershipFee;
    }

    public function notifierReglementEnLigneAuTresorier(string $cmd, float $total, string $autorisation, string $transaction, UserRepository $userRepository): ?bool
    {
        if (str_starts_with($cmd, 'F')) {
            // Facture
            $invoiceNumber = substr($cmd, 1);
            $cotisation = $this->getByInvoice($invoiceNumber);
            $type_personne = $cotisation->getUserType()->value;
            $id_personne = $cotisation->getUserId();

        } else {
            // Cotisation
            [$ref, $date, $type_personne, $id_personne, $reste] = explode('-', $cmd, 5);
        }

        $infos = [
            'id' => $id_personne,
            'type' => $type_personne,
            'nom' => 'N.C.',
            'prenom' => 'N.C.',
            'email' => 'N.C.',
        ];

        if ($type_personne == MemberType::MemberCompany->value) {
            if ($company = $this->companyMemberRepository?->get($id_personne)) {
                $infos['nom'] = $company->getLastName();
                $infos['prenom'] = $company->getFirstName();
                $infos['email'] = $company->getEmail();
            }
        } else {
            if ($user = $userRepository->get($id_personne)) {
                $infos['nom'] = $user->getLastName();
                $infos['prenom'] = $user->getFirstName();
                $infos['email'] = $user->getEmail();
            }
        }


        $sujet = "Paiement cotisation AFUP\n";

        $corps = "Bonjour, \n\n";
        $corps .= "Une cotisation annuelle AFUP a été réglée.\n\n";
        $corps .= "Personne : " . $infos['nom'] . " " . $infos['prenom'] . " (" . $infos['email'] . ")\n";
        $corps .= "URL : /admin/accounting/membership-fee/list/" . $infos['type'] . "/" . $infos['id'] . "\n";
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
                    $cotisation->getId(),
                    AFUP_COTISATIONS_REGLEMENT_ENLIGNE,
                    "autorisation : " . $autorisation . " / transaction : " . $transaction,
                );
        } elseif (substr(md5($reference), -3) === strtolower($verif) && !$this->isAlreadyPaid($cmd)) {
            [$ref, $date, $type_personne, $id_personne, $reste] = explode('-', (string) $cmd, 5);
            $date_debut = mktime(0, 0, 0, (int) substr($date, 2, 2), (int) substr($date, 0, 2), (int) substr($date, 4, 4));

            $cotisation = $this->getLastestByUserTypeAndId(MemberType::from((int) $type_personne), (int) $id_personne);
            $date_fin_precedente = !$cotisation instanceof MembershipFee ? 0 : $cotisation->getEndDate()->getTimestamp();

            if ($date_fin_precedente > 0) {
                $date_debut = strtotime('+1day', $date_fin_precedente);
            }

            $date_fin = $this->getNextSubscriptionExpiration($cotisation)->format('U');
            $result = $this->ajouter(
                MemberType::from((int) $type_personne),
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
     * @return array{type: int, id: int}
     */
    public function getAccountFromCmd(string $cmd): array
    {
        $arr = explode('-', $cmd, 5);
        // Depuis une facture : $cmd=FCOTIS-2023-202
        if (3 === count($arr)) {
            return ['type' => MemberType::MemberCompany->value, 'id' => (int) $arr[2]];
        }

        // Depuis une cotisation : $cmd=C2023-211120232237-0-5-PAUL-431
        [$ref, $date, $memberType, $memberId, $stuff] = $arr;

        return ['type' => (int) $memberType, 'id' => (int) $memberId];
    }

    /**
     * Supprime une cotisation
     *
     * @param $id Identifiant de la cotisation à supprimer
     * @return bool Succès de la suppression
     */
    public function supprimer(int $id): bool
    {
        try {
            $cotisation = $this->membershipFeeRepository->get($id);
            $this->membershipFeeRepository->delete($cotisation);
            return true;
        } catch (\Exception $exception) {
            return false;
        }
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

        $table = $cotisation['type_personne'] == MemberType::MemberCompany->value ? 'afup_personnes_morales' : 'afup_personnes_physiques';
        $requete = 'SELECT * FROM ' . $table . ' WHERE id=' . $cotisation['id_personne'];
        $personne = $this->_bdd->obtenirEnregistrement($requete);

        if ($cotisation['date_facture'] !== null) {
            $dateFacture = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $cotisation['date_facture']);
        } else {
            $dateFacture = \DateTimeImmutable::createFromFormat('U', $cotisation['date_debut']);
        }

        $bankAccountFactory = new BankAccountFactory();
        $isSubjectedToVat = Vat::isSubjectedToVat($dateFacture);
        // Construction du PDF
        $pdf = new PDF_Facture($bankAccountFactory->createApplyableAt($dateFacture), $isSubjectedToVat);
        $pdf->AddPage();

        $pdf->Cell(130, 5);
        $pdf->Cell(60, 5, 'Le ' . $dateFacture->format('d/m/Y'));

        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();

        // A l'attention du client [adresse]
        $pdf->SetFont('Arial', 'BU', 10);
        $pdf->Cell(130, 5, 'Objet : Facture n°' . $cotisation['numero_facture']);
        $pdf->SetFont('Arial', '', 10);

        if ($cotisation['type_personne'] == MemberType::MemberCompany->value) {
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
                $cotisation['reference_client'],
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
            if ($cotisation['type_personne'] == MemberType::MemberCompany->value) {
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

            if ($cotisation['type_personne'] == MemberType::MemberCompany->value) {
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

        if ($personne['type_personne'] == MemberType::MemberCompany->value) {
            $company = $this->companyMemberRepository ? $this->companyMemberRepository->get($personne['id_personne']) : null;
            Assertion::notNull($company);
            $contactPhysique = [
                'nom' => $company->getLastName(),
                'prenom' => $company->getFirstName(),
                'email' => $company->getEmail(),
            ];
        } else {
            $user = $userRepository->get($personne['id_personne']);
            Assertion::notNull($user);
            $contactPhysique = [
                'nom' => $user->getLastName(),
                'prenom' => $user->getFirstName(),
                'email' => $user->getEmail(),
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
        $cotisation = $this->getLastestByUserTypeAndId(MemberType::from((int) $personne['type_personne']), (int) $personne['id_personne']);
        $pattern = str_replace(' ', '', $patternPrefix) . '_' . $numeroFacture . '_' . date('dmY', $cotisation->getStartDate()->getTimestamp()) . '.pdf';

        $message = new Message('Facture AFUP', null, new MailUser(
            $contactPhysique['email'],
            sprintf('%s %s', $contactPhysique['prenom'], $contactPhysique['nom']),
        ));
        $message->addAttachment(new Attachment(
            $cheminFacture,
            $pattern,
            'base64',
            'application/pdf',
        ));
        $ok = $mailer->sendTransactional($message, $corps);
        @unlink($cheminFacture);

        return $ok;
    }

    /**
     * Retourne la dernière cotisation d'une personne morale
     */
    public function getLastestByUserTypeAndId(MemberType $type_personne, int $id_personne): ?MembershipFee
    {
        return $this->membershipFeeRepository->getLastestByUserTypeAndId($type_personne, $id_personne);
    }

    public function getNextSubscriptionExpiration(?MembershipFee $cotisation = null): DateTime
    {
        $endSubscription = $cotisation ? $cotisation->getEndDate() : new DateTime();
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
     * @param $invoiceId Identifiant de la facture
     * @param $token Token de la facture. Si null, pas de vérification
     */
    public function getByInvoice(string $invoiceId, string $token = null): ?MembershipFee
    {
        $criterias = ['invoiceNumber' => $invoiceId];
        if ($token !== null) {
            $criterias['token'] = $token;
        }
        return $this->membershipFeeRepository->getOneBy($criterias);
    }

    /**
     * Modifie une cotisation
     *
     * @param $id Identifiant de la cotisation à modifier
     * @param $type_reglement Type de règlement (espèces, chèque, virement)
     * @param $informations_reglement Informations concernant le règlement (numéro de chèque, de virement etc.)
     * @return bool Succès de la modification
     */
    public function updatePayment(int $id, int $type_reglement, string $informations_reglement): bool
    {
        return $this->membershipFeeRepository->updatePayment($id, $type_reglement, $informations_reglement) !== false;
    }

    public function isCurrentUserAllowedToReadInvoice(string $invoiceId): bool
    {
        if (!$this->_droits) {
            throw new \RuntimeException('La variable $_droits ne doit pas être null.');
        }

        $cotisation = $this->membershipFeeRepository->get($invoiceId);
        if (!$cotisation instanceof MembershipFee) {
            return false;
        }

        /**
         * si type_personne = 0, alors personne physique: id_personne doit être identique l'id de l'utilisateur connecté
         */
        if ($cotisation->getUserType() == MemberType::MemberPhysical) {
            return $cotisation->getUserId() == $this->_droits->obtenirIdentifiant();
        }

        /**
         * si type_personne = 1, alors personne morale: id_personne doit être égale à compagnyId de l'utilisateur connecté
         * qui doit aussi avoir le droit "ROLE_COMPAGNY_MANAGER"
         */
        if ($cotisation->getUserType() == MemberType::MemberCompany) {
            return $this->_droits->verifierDroitManagerPersonneMorale($cotisation->getUserId());
        }

        return false;
    }

    public function setCompanyMemberRepository(CompanyMemberRepository $companyMemberRepository): void
    {
        $this->companyMemberRepository = $companyMemberRepository;
    }

    public function setMembershipFeeRepository(MembershipFeeRepository $membershipFeeRepository): void
    {
        $this->membershipFeeRepository = $membershipFeeRepository;
    }
}
