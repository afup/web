<?php

namespace AppBundle\Offices;

use AppBundle\Association\Model\User;
use AppBundle\Event\Model\Invoice;
use Geocoder\Exception\NoResult;
use Geocoder\Geocoder;
use Geocoder\Model\Coordinates;

class OfficeFinder
{
    const MAX_DISTANCE_TO_OFFICE = 50000;

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
     */
    public function __construct(Geocoder $geocoder)
    {
        $this->geocoder = $geocoder;
        $this->officesCollection = new OfficesCollection();
    }

    /**
     * @param Invoice $invoice
     * @param User $user
     *
     * @return string|null
     */
    public function findOffice(Invoice $invoice, User $user = null)
    {
        $infos = $this->findInfos($invoice, $user);

        if (!isset($infos['office'])) {
            return null;
        }

        return $infos['office'];
    }

    /**
     * @param Invoice $invoice
     * @param User $user
     *
     * @return array
     */
    protected function findInfos(Invoice $invoice, User $user = null)
    {
        $coordinates = $this->findCoordinates($invoice, $user);

        $infos = [];

        if (null === $coordinates) {
            $infos['error'] = 'Coordonnées non trouvées';
        } else {
            try {
                $infosNearest = $this->locateNearestLocalOffice($coordinates);
                $infos['distance'] = $infosNearest['distance'];
                if ($infos['distance'] > self::MAX_DISTANCE_TO_OFFICE) {
                    $infos['error'] = "Trop éloigné d'une antenne";
                } else {
                    $infos['office'] = $infosNearest['key'];
                }
            } catch (\Exception $e) {
                $infos['error'] = $e->getMessage();
            }
        }

        return $infos;
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
        $latFrom = deg2rad((float) $latitudeFrom);
        $lonFrom = deg2rad((float) $longitudeFrom);
        $latTo = deg2rad((float) $latitudeTo);
        $lonTo = deg2rad((float) $longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }
}
