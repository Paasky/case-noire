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
     * @param float $lat
     * @param float $lng
     * @param string[] $types
     * @return LocationBlueprint[]
     */
    public function findInCoords(float $lat, float $lng, array $types = []): array
    {
        return $this->api->findByLatLng($lat, $lng, $types);
    }

    /**
     * @param Point $randomCenter
     * @param int $maxRangeMeters
     * @param int $minRangeMeters
     * @param string[] $types
     * @return LocationBlueprint[]
     */
    public function findInRange(
        Point $randomCenter,
        int $maxRangeMeters,
        int $minRangeMeters = 0,
        array $types = []
    ): array {
        $randomCenter = LocationManager::getRandomPoint($randomCenter, $maxRangeMeters, $minRangeMeters);

        $blueprints = [];
        foreach ($types as $type) {
            $blueprints = array_merge(
                $blueprints,
                $this->api->findByLatLng($randomCenter->getLat(), $randomCenter->getLng(), [$type])
            );
        }
        return $blueprints;
    }
}