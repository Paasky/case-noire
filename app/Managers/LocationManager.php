<?php

namespace App\Managers;

use App\Locations\Geolocator;
use App\Models\AgencyCase;
use App\Models\Common\CaseNoireModel;
use App\Models\Common\HasAndSpawnsInstances;
use App\Models\Common\HasLocation;
use App\Models\Location;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Support\Collection;

class LocationManager
{
    /**
     * @param AgencyCase $agencyCase
     * @param CaseNoireModel|HasAndSpawnsInstances $forModel
     * @return Location
     * @throws \Exception
     */
    public static function get(AgencyCase $agencyCase, CaseNoireModel $forModel): Location
    {
        $location = static::getSpawnCenterLocation($agencyCase, $forModel);

        $reservedIds = $agencyCase->locations->pluck('id')->add($agencyCase->location_id);
        $locationSettings = $forModel->location_settings;

        // 1) Find known locations 1st
        $existingLocations = Location
            ::inRange(
                $location->coords,
                $locationSettings->getMaxRange(),
                $locationSettings->getMinRange()
            )
            ->whereNotIn('id', $reservedIds)
            ->get();

        if ($existingLocations->count()) {
            return $existingLocations->random();
        }

        // 2) Try 10 times to find locations
        $attempts = 0;
        do {
            $locations = static::findLocations(
                $location->coords,
                $locationSettings->getMaxRange(),
                $locationSettings->getMinRange(),
                $locationSettings->getAllowedTypes(),
                $reservedIds->all()
            );
            if ($locations->count()) {
                return $locations->random();
            }
            $attempts++;
        } while($attempts < 10);

        throw new \Exception("Could not find any locations for Agency Case [{$agencyCase->nameForDebug()}], for model [{$forModel->nameForDebug()}]");
    }

    /**
     * @param AgencyCase $agencyCase
     * @param CaseNoireModel|HasAndSpawnsInstances $forModel
     * @return Location
     * @throws \Exception
     */
    public static function getSpawnCenterLocation(AgencyCase $agencyCase, CaseNoireModel $forModel): Location
    {
        $locationSettings = $forModel->location_settings;
        $centerType = $locationSettings->getSpawnCenterAtType();
        $centerName = $locationSettings->getSpawnCenterAtTypeName();

        if ($centerType == AgencyCase::class) {
            $centerModel = $agencyCase;
        } else {
            foreach ($agencyCase->modelInstances->where('model_type', $centerType) as $modelInstance) {
                if (!$centerName) {
                    $centerModel = $modelInstance;
                    break;
                }
                /** @noinspection PhpUndefinedFieldInspection */
                if ($centerName == $modelInstance->model->name ?? null) {
                    $centerModel = $modelInstance;
                    break;
                }
            }
        }

        if (!isset($centerModel)) {
            $center = $centerType;
            if ($centerName) {
                $center .= " $centerName";
            }
            throw new \InvalidArgumentException("Case [{$agencyCase->nameForDebug()}] does not have required center [$center] for a location");
        }

        /** @var HasLocation $centerModel */
        if (!isset($centerModel->location)) {
            throw new \InvalidArgumentException("Required center model [{$centerModel->nameForDebug()}] does not have a location");
        }

        return $centerModel->location;
    }

    /**
     * @param Point $center
     * @param int $maxRange
     * @param int $minRange
     * @param array $types
     * @param array $reservedIds
     * @return Collection|Location[]
     */
    protected static function findLocations(
        Point $center,
        int $maxRange,
        int $minRange = 0,
        array $types = Location::TYPES,
        array $reservedIds = []
    ): Collection {
        $gelocator = new Geolocator();
        $blueprints = $gelocator->findInRange($center, $maxRange, $minRange, $types);

        $locations = [];
        foreach ($blueprints as $blueprint) {
            $existingLocation = $blueprint->getExistingModel();
            if ($existingLocation) {
                // existing location is not reserved -> use it
                if (!in_array($existingLocation->id, $reservedIds)) {
                    $locations[] = $existingLocation;
                }
                // it's already reserved -> continue to the next found location
                continue;
            }

            // no existing location -> create it
            $locations[] = Location::create($blueprint->getModelParams());
        }

        return collect($locations);
    }

    public static function getRandomPoint(Point $center, int $maxRangeMeters, int $minRangeMeters = 0): Point
    {
        $distance = rand($minRangeMeters, $maxRangeMeters);
        $bearing = deg2rad(rand(0, 359));
        $centerLat = deg2rad($center->getLat());
        $centerLng = deg2rad($center->getLng());

        $distanceDividedByEarthRadius = $distance / Location::EARTH_RADIUS_IN_M;

        $randomLat = rad2deg(asin(
            sin($centerLat) * cos($distanceDividedByEarthRadius) +
            cos($centerLat) * sin($distanceDividedByEarthRadius) * cos($bearing)
        ));
        $randomLng = rad2deg($centerLng + atan2(
            sin($bearing) * sin($distanceDividedByEarthRadius) * cos($centerLat),
            cos($distanceDividedByEarthRadius) - sin($centerLat) * sin($randomLat)
        ));

        return new Point($randomLat, $randomLng);
    }

    public static function getDistanceInMeters(Point $from, Point $to): int
    {
        $lat1 = $from->getLat();
        $lat2 = $to->getLat();
        $lng1 = $from->getLng();
        $lng2 = $to->getLng();

        if (($lat1 == $lat2) && ($lng1 == $lng2)) {
            return 0;
        }
        else {
            $theta = $lng1 - $lng2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            return round($dist * 111189.57696);
        }
    }
}