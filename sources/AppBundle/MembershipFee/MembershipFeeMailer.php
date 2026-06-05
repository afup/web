<?php

declare(strict_types=1);

namespace AppBundle\MembershipFee;

use AppBundle\Afup;
use AppBundle\Association\MemberType;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Email\Mailer\Attachment;
use AppBundle\Email\Mailer\Mailer;
use AppBundle\Email\Mailer\MailUser;
use AppBundle\Email\Mailer\MailUserFactory;
use AppBundle\Email\Mailer\Message;
use AppBundle\MembershipFee\Model\Repository\MembershipFeeRepository;
use Webmozart\Assert\Assert;

final readonly class MembershipFeeMailer
{
    public function __construct(
        private MembershipFeeRepository $membershipFeeRepository,
        private UserRepository $userRepository,
        private CompanyMemberRepository $companyMemberRepository,
        private Mailer $mailer,
        private MembershipFeeInvoicePdfGenerator $pdfGenerator,
    ) {}

    /**
     * Envoi par mail d'une facture au format PDF
     *
     * @param $idCotisation Identifiant de la cotisation
     * @return bool Succès de l'envoi
     */
    public function envoyerFacture(int $idCotisation): bool
    {
        $membership = $this->membershipFeeRepository->get($idCotisation);

        if ($membership->getUserType() === MemberType::MemberCompany) {
            $company = $this->companyMemberRepository->get($membership->getUserId());
            Assert::notNull($company);
            $contactPhysique = [
                'nom' => $company->getLastName(),
                'prenom' => $company->getFirstName(),
                'email' => $company->getEmail(),
            ];
        } else {
            $user = $this->userRepository->get($membership->getUserId());
            Assert::notNull($user);
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
        $corps .= Afup::RAISON_SOCIALE . "<br />";
        $corps .= Afup::ADRESSE . "<br />";
        $corps .= Afup::CODE_POSTAL . " " . Afup::VILLE . "<br />";

        $cheminFacture = AFUP_CHEMIN_RACINE . 'cache/fact' . $idCotisation . '.pdf';
        $numeroFacture = $this->pdfGenerator->genererFacture($idCotisation, $cheminFacture);
        $pattern = str_replace(' ', '', $patternPrefix) . '_' . $numeroFacture . '_' . date('dmY', $membership->getStartDate()->getTimestamp()) . '.pdf';

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
        $ok = $this->mailer->sendTransactional($message, $corps);
        @unlink($cheminFacture);

        return $ok;
    }

    public function notifierReglementEnLigneAuTresorier(string $cmd, float $total, string $autorisation, string $transaction): bool
    {
        if (str_starts_with($cmd, 'F')) {
            // Facture
            $invoiceNumber = substr($cmd, 1);
            $cotisation = $this->membershipFeeRepository->getOneBy(['invoiceNumber' => $invoiceNumber]);
            $typePersonne = $cotisation->getUserType()->value;
            $idPersonne = $cotisation->getUserId();
        } else {
            // Cotisation
            [$ref, $date, $typePersonne, $idPersonne, $reste] = explode('-', $cmd, 5);
        }

        $infos = [
            'id' => $idPersonne,
            'type' => $typePersonne,
            'nom' => 'N.C.',
            'prenom' => 'N.C.',
            'email' => 'N.C.',
        ];

        if ($typePersonne == MemberType::MemberCompany->value) {
            if ($company = $this->companyMemberRepository->get($idPersonne)) {
                $infos['nom'] = $company->getLastName();
                $infos['prenom'] = $company->getFirstName();
                $infos['email'] = $company->getEmail();
            }
        } else {
            if ($user = $this->userRepository->get($idPersonne)) {
                $infos['nom'] = $user->getLastName();
                $infos['prenom'] = $user->getFirstName();
                $infos['email'] = $user->getEmail();
            }
        }

        $sujet = "Paiement cotisation AFUP";

        $corps = "Bonjour, \n\n";
        $corps .= "Une cotisation annuelle AFUP a été réglée.\n\n";
        $corps .= "Personne : " . $infos['nom'] . " " . $infos['prenom'] . " (" . $infos['email'] . ")\n";
        $corps .= "URL : /admin/accounting/membership-fee/list/" . $infos['type'] . "/" . $infos['id'] . "\n";
        $corps .= "Commande : " . $cmd . "\n";
        $corps .= "Total : " . $total . "\n";
        $corps .= "Autorisation : " . $autorisation . "\n";
        $corps .= "Transaction : " . $transaction . "\n\n";

        $message = new Message($sujet, new MailUser(MailUser::DEFAULT_SENDER_EMAIL, MailUser::DEFAULT_SENDER_NAME), MailUserFactory::tresorier());
        return $this->mailer->sendTransactional($message, $corps);
    }
}
