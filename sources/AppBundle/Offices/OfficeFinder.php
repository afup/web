<?php

declare(strict_types=1);

namespace AppBundle\Offices;

use AppBundle\Antennes\AntennesCollection;
use AppBundle\Association\Model\User;
use AppBundle\Event\Model\Invoice;
use Geocoder\Geocoder;
use Geocoder\Model\AddressCollection;
use Geocoder\Model\Coordinates;

class OfficeFinder
{
    public const MAX_DISTANCE_TO_OFFICE = 50000;

    private readonly AntennesCollection $antennesCollection;

    private array $geocodeCache = [];

    public function __construct(private readonly Geocoder $geocoder)
    {
        $this->antennesCollection = new AntennesCollection();
    }

    public function findOffice(Invoice $invoice, User $user = null): ?string
    {
        $infos = $this->findInfos($invoice, $user);

        return $infos['office'] ?? null;
    }

    protected function findInfos(Invoice $invoice, User $user = null): array
    {
        $coordinates = $this->findCoordinates($invoice, $user);

        $infos = [];

        if (!$coordinates instanceof Coordinates) {
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

    protected function findCoordinates(Invoice $invoice, User $user = null): ?Coordinates
    {
        if ($user instanceof User) {
            if ($user->getCountry() !== 'FR') {
                return null;
            }

            $addressCollection = $this->geocodeAddresses([
                $user->getZipCode() . ' ' . $user->getCity() . ' ' . $user->getCountry(),
                $user->getZipCode() . ' ' . $user->getCity(),
                $user->getCity(),
            ]);
        } else {
            $addressCollection = $this->geocodeAddresses([
                $invoice->getZipCode() . ' ' . $invoice->getCity() . ' ' . $invoice->getCountryId(),
                $invoice->getZipCode() . ' ' . $invoice->getCity(),
                $invoice->getCity(),
            ]);
        }

        if (!$addressCollection instanceof AddressCollection) {
            return null;
        }

        return $addressCollection->first()->getCoordinates();
    }

    private function geocodeAddresses(array $addresses): ?AddressCollection
    {
        foreach ($addresses as $address) {
            if ($result = $this->geocode($address)) {
                return $result;
            }
        }

        return null;
    }

    private function geocode(string $address): ?AddressCollection
    {
        if (false !== stripos($address, 'n/a')) {
            return null;
        }

        $address = str_replace('cedex', '', strtolower($address));
        $address = trim(trim($address), '-');

        if ($address === '') {
            return null;
        }

        return $this->geocodeCache[$address] ?? $this->geocodeCache[$address] = $this->geocoder->geocode($address);
    }

    protected function locateNearestLocalOffice(Coordinates $coordinates): array
    {
        $localOfficesDistance = [];

        foreach ($this->antennesCollection->getAll() as $antenne) {
            if (null === $antenne->map) {
                continue;
            }

            $localOfficesDistance[$antenne->code] = $this->haversineGreatCircleDistance(
                $coordinates->getLatitude(),
                $coordinates->getLongitude(),
                $antenne->map->firstCity->position->latitude,
                $antenne->map->firstCity->position->longitude,
            );
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
    private function haversineGreatCircleDistance(float $latitudeFrom, float $longitudeFrom, float $latitudeTo, float $longitudeTo, int $earthRadius = 6371000): float
    {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(sin($latDelta / 2) ** 2 +
                cos($latFrom) * cos($latTo) * sin($lonDelta / 2) ** 2));
        return $angle * $earthRadius;
    }
}
