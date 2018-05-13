<?php

namespace AppBundle\Event\Ticket;

use Afup\Site\Association\Cotisations;
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
     * @var Cotisations
     */
    private $cotisations;

    /**
     * @var InvoiceRepository
     */
    private $invoiceRepository;

    /**
     * @param OfficeFinder $officeFinder
     * @param Inscriptions $inscriptions
     * @param Cotisations $cotisations
     * @param InvoiceRepository $invoiceRepository
     * @param UserRepository $userRepository
     */
    public function __construct(OfficeFinder $officeFinder, Inscriptions $inscriptions, Cotisations $cotisations, InvoiceRepository $invoiceRepository, UserRepository $userRepository)
    {
        $this->officeFinder = $officeFinder;
        $this->inscriptions = $inscriptions;
        $this->cotisations = $cotisations;
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
            if ($inscriptionsDataRow['etat'] == AFUP_FORUM_ETAT_ANNULE) {
                // On n'exporte pas les billets inscriptions annulées
                continue;
            }

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
        if ($user->isMemberForCompany()) {
            return 0;
        }

        $computer = new SeniorityComputer($this->cotisations);
        return $computer->computeSeniority($user)->y;
    }

    /**
     * @param $type
     * @return mixed|null|string
     */
    private function getTypePass($type)
    {
        switch ($type) {
            case AFUP_FORUM_PREMIERE_JOURNEE:
            case AFUP_FORUM_LATE_BIRD_PREMIERE_JOURNEE:
                return 'PASS JOUR 1';
                break;
            case AFUP_FORUM_DEUXIEME_JOURNEE:
            case AFUP_FORUM_LATE_BIRD_DEUXIEME_JOURNEE:
                return 'PASS JOUR 2';
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
                return 'PASS 2 JOURS';
                break;
            case AFUP_FORUM_ORGANISATION:
                return 'ORGANISATION';
                break;
            case AFUP_FORUM_PRESSE:
                return 'PRESSE';
                break;
            case AFUP_FORUM_CONFERENCIER:
                return 'CONFERENCIER';
                break;
            case AFUP_FORUM_SPONSOR:
                return 'SPONSOR';
                break;
            default:
                throw new \RuntimeException(sprintf('Libellé du type %s non trouvé', var_export($type, true)));
                break;
        }
    }
}
