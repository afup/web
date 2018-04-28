<?php

namespace AppBundle\Offices;

use Afup\Site\Forum\Inscriptions;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Invoice;
use AppBundle\Event\Model\Repository\InvoiceRepository;
use AppBundle\Event\Model\Ticket;
use Geocoder\Exception\NoResult;
use Geocoder\Geocoder;
use Geocoder\Model\Coordinates;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class OfficeFinder
{
    const MAX_DISTANCE_TO_OFFICE = 50000;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var InvoiceRepository
     */
    private $invoiceRepository;

    /**
     * @var Inscriptions
     */
    private $inscriptions;

    /**
     * @var OfficesCollection
     */
    private $officesCollection;

    /**
     * @var Geocoder
     */
    private $geocoder;

    /**
     * @var array
     */
    private $geocodeCache = [];

    /**
     * @param Geocoder $geocoder
     * @param UserRepository $userRepository
     * @param InvoiceRepository $invoiceRepository
     * @param Inscriptions $inscriptions
     */
    public function __construct(Geocoder $geocoder, UserRepository $userRepository, InvoiceRepository $invoiceRepository, Inscriptions $inscriptions)
    {
        $this->geocoder = $geocoder;
        $this->userRepository = $userRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->inscriptions = $inscriptions;
        $this->officesCollection = new OfficesCollection();
    }

    /**
     * @param Event $event
     *
     * @return \Generator
     */
    public function getFromRegistrationsOnEvent(Event $event)
    {
        $inscriptionsData = $this->inscriptions->obtenirListe($event->getId());
        foreach ($inscriptionsData as $inscriptionsDataRow) {
            $invoice = $this->invoiceRepository->getByReference($inscriptionsDataRow['reference']);

            try {
                $user = $this->userRepository->loadUserByUsername($inscriptionsDataRow['email']);
            } catch (UsernameNotFoundException $exception) {
                $user = null;
            }

            $yeatCotis = null;

            if ($user !== null) {
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

                $yeatCotis = $totalDiffs->y;
            }

            preg_match('@\<tag\>(.*)\</tags?\>@i', $inscriptionsDataRow['commentaires'], $matches);
            $tags =  isset($matches[1]) ? $matches[1] : '';
            $tags = explode(';',$tags);
            $tags = implode(' - ',array_filter($tags));


            $row = [
                'id' => $inscriptionsDataRow['id'],
                'reference' => $invoice->getReference(),
                'prenom' => $inscriptionsDataRow['prenom'],
                'nom' => $inscriptionsDataRow['nom'],
                'societe' => $invoice->getCompany(),
                'tags' => $tags,
                'type_pass' => $this->getTypePass($inscriptionsDataRow['type_inscription']),
                'email' => $inscriptionsDataRow['email'],
                'member_since' => $yeatCotis,
                'office' => null,
                'distance' => null,
                'error' => null,
                'city' => null !== $user ? $user->getCity() : $invoice->getCity(),
                'zip_code' => null !== $user ? $user->getZipCode() : $invoice->getZipCode(),
                'country' => null !== $user ? $user->getCountry() : $invoice->getCountryId(),
            ];

            $coordinates = $this->findCoordinates($invoice, $user);

            if (null === $coordinates) {
                $row['error'] = 'Coordonnées non trouvées';
            } else {
                try {
                    $infosNearest = $this->locateNearestLocalOffice($coordinates);
                    $row['distance'] = $infosNearest['distance'];
                    if ($row['distance'] > self::MAX_DISTANCE_TO_OFFICE) {
                        $row['error'] = "Trop éloigné d'une antenne";
                    } else {
                        $row['office'] = $infosNearest['key'];
                    }
                } catch (\Exception $e) {
                    $row['error'] = $e->getMessage();
                }
            }

            yield $row;
        }
    }

    private function getTypePass($type)
    {
        $AFUP_Tarifs_Forum_Lib = array(
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
        );

        $lib_pass = isset($AFUP_Tarifs_Forum_Lib[$type]) ? $AFUP_Tarifs_Forum_Lib[$type] : null;

        switch ($type)
        {
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

    /**
     * @param array $geocodeCache
     */
    public function setGeocodeCache($geocodeCache)
    {
        $this->geocodeCache = $geocodeCache;
    }

    /**
     * @param Invoice $invoice
     * @param User $user
     *
     * @return Coordinates
     */
    protected function findCoordinates(Invoice $invoice, User $user = null)
    {
        if (null !== $user) {
            if ($user->getCountry() != 'FR') {
                return null;
            }

            $adressCollection = $this->geocodeAdresses([
                $user->getZipCode() . ' ' . $user->getCity() . ' ' . $user->getCountry(),
                $user->getZipCode() . ' ' . $user->getCity(),
                $user->getCity()
            ]);
        } else {
            $adressCollection = $this->geocodeAdresses([
                $invoice->getZipCode() . ' ' . $invoice->getCity() . ' ' . $invoice->getCountryId(),
                $invoice->getZipCode() . ' ' . $invoice->getCity(),
                $invoice->getCity()
            ]);
        }

        if (null === $adressCollection) {
            return null;
        }

        $address = $adressCollection->first();
        return $address->getCoordinates();
    }

    /**
     * @param array $addresses
     *
     * @return \Geocoder\Model\AddressCollection|null
     */
    private function geocodeAdresses(array $addresses)
    {
        foreach ($addresses as $address) {
            try {
                return $this->geocode($address);
            } catch (NoResult $noResult) {
                continue;
            }
        }

        return null;
    }

    /**
     * @param string $address
     *
     * @return \Geocoder\Model\AddressCollection
     */
    private function geocode($address)
    {
        if (false !== stripos($address, 'n/a')) {
            return null;
        }

        $address = str_replace('cedex', '', strtolower($address));
        $address = trim(trim($address), '-');

        if (0 === strlen($address)) {
            return null;
        }

        if (isset($this->geocodeCache[$address])) {
            return $this->geocodeCache[$address];
        }

        return $this->geocodeCache[$address] = $this->geocoder->geocode($address);
    }

    /**
     * @param Coordinates $coordinates
     *
     * @return mixed
     */
    protected function locateNearestLocalOffice(Coordinates $coordinates)
    {
        $localOfficesDistance = [];

        foreach ($this->officesCollection->getAll() as $office => $localOffice) {
            $distance = $this->haversineGreatCircleDistance($coordinates->getLatitude(), $coordinates->getLongitude(), $localOffice['latitude'], $localOffice['longitude']);
            $localOfficesDistance[$office] = $distance;
        }

        asort($localOfficesDistance);

        $nearest = key($localOfficesDistance);

        return ['key' => $nearest, 'distance' => $localOfficesDistance[$nearest]];
    }

    /**
     * cf https://stackoverflow.com/questions/10053358/measuring-the-distance-between-two-coordinates-in-php
     *
     * Calculates the great-circle distance between two points, with
     * the Haversine formula.
     *
     * @param float $latitudeFrom Latitude of start point in [deg decimal]
     * @param float $longitudeFrom Longitude of start point in [deg decimal]
     * @param float $latitudeTo Latitude of target point in [deg decimal]
     * @param float $longitudeTo Longitude of target point in [deg decimal]
     * @param int $earthRadius Mean earth radius in [m]
     *
     * @return float Distance between points in [m] (same as earthRadius)
     */
    private function haversineGreatCircleDistance(
        $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
    {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }
}
