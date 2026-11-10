<?php

declare(strict_types=1);

namespace AppBundle\MembershipFee;

use AppBundle\Afup;
use AppBundle\Association\MemberType;
use AppBundle\Association\Entity\Repository\CompanyMemberRepository;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Email\Mailer\Attachment;
use AppBundle\Email\Mailer\Mailer;
use AppBundle\Email\Mailer\MailUser;
use AppBundle\Email\Mailer\MailUserFactory;
use AppBundle\Email\Mailer\Message;
use AppBundle\MembershipFee\Entity\Repository\CotisationRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Webmozart\Assert\Assert;

final readonly class MembershipFeeMailer
{
    public function __construct(
        private CotisationRepository $membershipFeeRepository,
        private UserRepository $userRepository,
        private CompanyMemberRepository $companyMemberRepository,
        private Mailer $mailer,
        private MembershipFeeInvoicePdfGenerator $pdfGenerator,
        #[Autowire('%kernel.project_dir%/htdocs/cache/')]
        private string $publicCacheDir,
    ) {}

    /**
     * Envoi par mail d'une facture au format PDF
     *
     * @return bool Succès de l'envoi
     */
    public function envoyerFacture(int $idCotisation): bool
    {
        $membership = $this->membershipFeeRepository->find($idCotisation);

        if ($membership->typePersonne === MemberType::MemberCompany) {
            $company = $this->companyMemberRepository->find($membership->idPersonne);
            Assert::notNull($company);
            $contactPhysique = [
                'nom' => $company->lastName,
                'prenom' => $company->firstName,
                'email' => $company->email,
            ];
        } else {
            $user = $this->userRepository->get($membership->idPersonne);
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

        $cheminFacture = $this->publicCacheDir . 'fact' . $idCotisation . '.pdf';
        $numeroFacture = $this->pdfGenerator->genererFacture($idCotisation, $cheminFacture);
        $pattern = str_replace(' ', '', $patternPrefix) . '_' . $numeroFacture . '_' . date('dmY', $membership->dateDebut->getTimestamp()) . '.pdf';

        $subject = 'Facture AFUP';
        if ($membership->typePersonne === MemberType::MemberCompany) {
            $subject .= ' - ' . $company->companyName;
        } else {
            $subject .= ' - ' . $contactPhysique['nom'] . ' ' . $contactPhysique['prenom'];
        }

        $message = new Message($subject, null, new MailUser(
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
            $cotisation = $this->membershipFeeRepository->findOneBy(['numeroFacture' => $invoiceNumber]);
            $typePersonne = $cotisation->typePersonne->value;
            $idPersonne = $cotisation->idPersonne;
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
            if (null !== $company = $this->companyMemberRepository->find((int) $idPersonne)) {
                $infos['nom'] = $company->lastName;
                $infos['prenom'] = $company->firstName;
                $infos['email'] = $company->email;
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
