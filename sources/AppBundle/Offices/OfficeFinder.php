<?php

namespace AppBundle\Offices;

use Afup\Site\Forum\Inscriptions;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Invoice;
use AppBundle\Event\Model\Repository\InvoiceRepository;
use Geocoder\Exception\NoResult;
use Geocoder\Geocoder;
use Geocoder\Model\Coordinates;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class OfficeFinder
{
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

            $row = [
                'reference' => $invoice->getReference(),
                'nearest' => null,
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
                    $row['nearest'] = $infosNearest['key'];
                    $row['distance'] = $infosNearest['distance'];
                } catch (\Exception $e) {
                    $row['error'] = $e->getMessage();
                }
            }

            yield $row;
        }
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

            try {
                $adressCollection = $this->geocode($user->getZipCode() . ' ' . $user->getCity());
            } catch (NoResult $noResult) {
                $adressCollection = $this->geocode($user->getCity());
            }
        } else {
            try {
                $adressCollection = $this->geocode($invoice->getZipCode() . ' ' . $invoice->getCity());
            } catch (NoResult $noResult) {
                $adressCollection = $this->geocode($invoice->getCity());
            }
        }

        if (null === $adressCollection) {
            return null;
        }

        $address = $adressCollection->first();
        return $address->getCoordinates();
    }

    /**
     * @param string $address
     *
     * @return \Geocoder\Model\AddressCollection
     */
    private function geocode($address)
    {
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
