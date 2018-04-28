<?php

namespace AppBundle\Event\Ticket;

use Afup\Site\Forum\Inscriptions;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\InvoiceRepository;
use AppBundle\Offices\OfficeFinder;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class RegistrationsExportGenerator
{
    /**
     * @var OfficeFinder
     */
    private $officeFinder;

    /**
     * @var Inscriptions
     */
    private $inscriptions;

    /**
     * @var InvoiceRepository
     */
    private $invoiceRepository;

    /**
     * @param OfficeFinder $officeFinder
     * @param Inscriptions $inscriptions
     * @param InvoiceRepository $invoiceRepository
     * @param UserRepository $userRepository
     */
    public function __construct(OfficeFinder $officeFinder, Inscriptions $inscriptions, InvoiceRepository $invoiceRepository, UserRepository $userRepository)
    {
        $this->officeFinder = $officeFinder;
        $this->inscriptions = $inscriptions;
        $this->invoiceRepository = $invoiceRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param Event $event
     * @param \SplFileObject $toFile
     */
    public function export(Event $event, \SplFileObject $toFile)
    {
        $columns = [
            'id',
            'reference',
            'prenom',
            'nom',
            'societe',
            'tags',
            'type_pass',
            'email',
            'member_since',
            'office'
        ];

        $toFile->fputcsv($columns);

        foreach ($this->getFromRegistrationsOnEvent($event) as $row) {
            $preparedRow = [];
            foreach ($columns as $column) {
                if (!array_key_exists($column, $row)) {
                    throw new \RuntimeException(sprintf('Colonne "%s" non trouvée : %s', $column, var_export($row, true)));
                }
                $preparedRow[] = $row[$column];
            }
            $toFile->fputcsv($preparedRow);
        }
    }

    /**
     * @param Event $event
     *
     * @return \Generator
     */
    protected function getFromRegistrationsOnEvent(Event $event)
    {
        $inscriptionsData = $this->inscriptions->obtenirListe($event->getId());
        foreach ($inscriptionsData as $inscriptionsDataRow) {
            $invoice = $this->invoiceRepository->getByReference($inscriptionsDataRow['reference']);

            try {
                $user = $this->userRepository->loadUserByUsername($inscriptionsDataRow['email']);
            } catch (UsernameNotFoundException $exception) {
                $user = null;
            }

            yield [
                'id' => $inscriptionsDataRow['id'],
                'reference' => $invoice->getReference(),
                'prenom' => $inscriptionsDataRow['prenom'],
                'nom' => $inscriptionsDataRow['nom'],
                'societe' => $invoice->getCompany(),
                'tags' => $this->extractAndCleanTags($inscriptionsDataRow['commentaires']),
                'type_pass' => $this->getTypePass($inscriptionsDataRow['type_inscription']),
                'email' => $inscriptionsDataRow['email'],
                'member_since' => null !== $user ? $this->comptureSeniority($user) : null,
                'office' => $this->officeFinder->findOffice($invoice, $user),
                'distance' => null,
                'error' => null,
                'city' => null !== $user ? $user->getCity() : $invoice->getCity(),
                'zip_code' => null !== $user ? $user->getZipCode() : $invoice->getZipCode(),
                'country' => null !== $user ? $user->getCountry() : $invoice->getCountryId(),
            ];
        }
    }

    /**
     * @param string $comments
     *
     * @return string
     */
    private function extractAndCleanTags($comments)
    {
        preg_match('@\<tag\>(.*)\</tags?\>@i', $comments, $matches);
        $tags =  isset($matches[1]) ? $matches[1] : '';
        $tags = explode(';', $tags);

        return implode(' - ', array_filter($tags));
    }

    /**
     * @param User $user
     *
     * @return int
     */
    private function comptureSeniority(User $user)
    {
        $cotisations = new \Afup\Site\Association\Cotisations($GLOBALS['AFUP_DB']);
        $cotis = $cotisations->obtenirListe(AFUP_PERSONNES_PHYSIQUES, $user->getId());
        $now = new \DateTime();
        $diffs = [];

        foreach ($cotis as $coti) {
            $from = \DateTimeImmutable::createFromFormat('U', $coti['date_debut']);
            $to = \DateTimeImmutable::createFromFormat('U', $coti['date_fin']);
            $to = min($now, $to);
            $diffs[] = $from->diff($to);
        }

        $reference = new \DateTimeImmutable();
        $lastest = clone $reference;
        foreach ($diffs as $dif) {
            $lastest = $lastest->add($dif);
        }

        $totalDiffs = $reference->diff($lastest);

        return $totalDiffs->y;
    }

    /**
     * @param $type
     * @return mixed|null|string
     */
    private function getTypePass($type)
    {
        $AFUP_Tarifs_Forum_Lib = [
            AFUP_FORUM_INVITATION => 'Invitation',
            AFUP_FORUM_ORGANISATION => 'Organisation',
            AFUP_FORUM_PROJET => 'Projet PHP',
            AFUP_FORUM_SPONSOR => 'Sponsor',
            AFUP_FORUM_PRESSE => 'Presse',
            AFUP_FORUM_PROF => 'Enseignement supérieur',
            AFUP_FORUM_CONFERENCIER => 'Conferencier',
            AFUP_FORUM_PREMIERE_JOURNEE => 'Jour 1 ',
            AFUP_FORUM_DEUXIEME_JOURNEE => 'Jour 2',
            AFUP_FORUM_2_JOURNEES => '2 Jours',
            AFUP_FORUM_2_JOURNEES_AFUP => '2 Jours AFUP',
            AFUP_FORUM_PREMIERE_JOURNEE_AFUP => 'Jour 1 AFUP',
            AFUP_FORUM_DEUXIEME_JOURNEE_AFUP => 'Jour 2 AFUP',
            AFUP_FORUM_2_JOURNEES_ETUDIANT => '2 Jours Etudiant',
            AFUP_FORUM_PREMIERE_JOURNEE_ETUDIANT => 'Jour 1 Etudiant',
            AFUP_FORUM_DEUXIEME_JOURNEE_ETUDIANT => 'Jour 2 Etudiant',
            AFUP_FORUM_2_JOURNEES_PREVENTE => '2 Jours prévente',
            AFUP_FORUM_2_JOURNEES_AFUP_PREVENTE => '2 Jours AFUP prévente',
            AFUP_FORUM_2_JOURNEES_PREVENTE_ADHESION => '2 Jours prévente + adhésion',
            AFUP_FORUM_2_JOURNEES_ETUDIANT_PREVENTE => '2 Jours Etudiant prévente',
            AFUP_FORUM_2_JOURNEES_COUPON => '2 Jours avec coupon de réduction',
            AFUP_FORUM_2_JOURNEES_SPONSOR => '2 Jours par Sponsor',
            AFUP_FORUM_PREMIERE_JOURNEE_ETUDIANT_PREVENTE => '',
            AFUP_FORUM_DEUXIEME_JOURNEE_ETUDIANT_PREVENTE => '',
            AFUP_FORUM_SPECIAL_PRICE => 'Tarif Spécial',
        ];

        $lib_pass = isset($AFUP_Tarifs_Forum_Lib[$type]) ? $AFUP_Tarifs_Forum_Lib[$type] : null;

        switch ($type) {
            case AFUP_FORUM_PREMIERE_JOURNEE:
            case AFUP_FORUM_LATE_BIRD_PREMIERE_JOURNEE:
                $lib_pass = 'PASS JOUR 1';
                break;
            case AFUP_FORUM_DEUXIEME_JOURNEE:
            case AFUP_FORUM_LATE_BIRD_DEUXIEME_JOURNEE:
                $lib_pass = 'PASS JOUR 2';
                break;
            case AFUP_FORUM_2_JOURNEES:
            case AFUP_FORUM_2_JOURNEES_AFUP:
            case AFUP_FORUM_2_JOURNEES_ETUDIANT:
            case AFUP_FORUM_2_JOURNEES_PREVENTE:
            case AFUP_FORUM_2_JOURNEES_AFUP_PREVENTE:
            case AFUP_FORUM_2_JOURNEES_ETUDIANT_PREVENTE:
            case AFUP_FORUM_2_JOURNEES_COUPON:
            case AFUP_FORUM_INVITATION:
            case AFUP_FORUM_EARLY_BIRD:
            case AFUP_FORUM_EARLY_BIRD_AFUP:
            case AFUP_FORUM_LATE_BIRD:
            case AFUP_FORUM_LATE_BIRD_AFUP:
            case AFUP_FORUM_CFP_SUBMITTER:
            case AFUP_FORUM_SPECIAL_PRICE:
                $lib_pass = 'PASS 2 JOURS';
                break;
            case AFUP_FORUM_ORGANISATION:
            case AFUP_FORUM_PRESSE:
            case AFUP_FORUM_CONFERENCIER:
            case AFUP_FORUM_SPONSOR:
                $lib_pass = strtoupper($AFUP_Tarifs_Forum_Lib[$type]);
                break;

            default:
                ;
                break;
        }

        return $lib_pass;
    }
}
