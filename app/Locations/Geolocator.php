<?php

namespace App\Locations;

use App\Blueprints\LocationBlueprint;
use App\Managers\LocationManager;
use App\Models\Location;
use Grimzy\LaravelMysqlSpatial\Types\Point;

class Geolocator
{
    /** @var MapApi */
    protected $api;

    public function __construct(MapApi $api = null)
    {
        $this->api = $api ?: new MapBoxApi();
    }

    /**
     * @param Point $center
     * @param int $maxRangeMeters
     * @param int $minRangeMeters
     * @param string[] $types
     * @return LocationBlueprint[]
     */
    public function findInRange(
        Point $center,
        int $maxRangeMeters,
        int $minRangeMeters = 0,
        array $types = Location::TYPES
    ): array {
        $center = LocationManager::getRandomPoint($center, $maxRangeMeters, $minRangeMeters);

        $blueprints = [];
        foreach ($types ?: Location::TYPES as $type) {
            $blueprints = array_merge(
                $blueprints,
                $this->api->findByLatLng($center->getLat(), $center->getLng(), [$type])
            );
        }
        return $blueprints;
    }
}