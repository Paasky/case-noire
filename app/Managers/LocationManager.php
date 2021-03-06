<?php

namespace App\Managers;

use App\Locations\Geolocator;
use App\Models\AgencyCase;
use App\Models\Common\CaseNoireModel;
use App\Models\Common\HasAndSpawnsInstances;
use App\Models\Common\HasCoordinates;
use App\Models\Common\HasLocation;
use App\Models\Location;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Support\Collection;

class LocationManager
{
    /**
     * @param AgencyCase $agencyCase
     * @param CaseNoireModel|HasAndSpawnsInstances $model
     * @return Location
     * @throws \Exception
     */
    public static function getForCaseModel(AgencyCase $agencyCase, CaseNoireModel $model): Location
    {
        $locationSettings = $model->location_settings;
        $centerModel = static::getCenterModel($agencyCase, $model, true);
        $centerLocation = static::getCenterLocation($centerModel);

        $notIds = $agencyCase->locations->pluck('id')
            ->add($agencyCase->location_id)
            ->diff([$centerLocation->id]) // allow spawning on the center location
            ->all();

        try {
            return static::get(
                $centerModel->coords,
                $locationSettings->getMaxRange(),
                $locationSettings->getMinRange(),
                $locationSettings->getAllowedTypes(),
                $notIds
            );
        } catch (\InvalidArgumentException $e) {
            throw stristr($e->getMessage(), 'Could not find any locations within') ?
                new \Exception("{$e->getMessage()} for Agency Case [{$agencyCase->nameForDebug()}], for model [{$model->nameForDebug()}]") :
                $e;
        }
    }

    /**
     * @param AgencyCase $agencyCase
     * @param CaseNoireModel|HasAndSpawnsInstances $forModel
     * @param bool $orFail
     * @return CaseNoireModel|HasLocation|HasCoordinates|null
     */
    public static function getCenterModel(AgencyCase $agencyCase, CaseNoireModel $forModel, bool $orFail = false): ?CaseNoireModel
    {
        $locationSettings = $forModel->location_settings;
        $centerType = $locationSettings->getSpawnAtClass();
        $centerId = $locationSettings->getSpawnAtId();

        if ($centerType == AgencyCase::class) {
            $centerModel = $agencyCase;
        } else {
            foreach ($agencyCase->modelInstances->where('model_type', $centerType) as $modelInstance) {
                if (!$centerId) {
                    $centerModel = $modelInstance;
                    break;
                }
                if ($centerId == $modelInstance->model->name ?? null) {
                    $centerModel = $modelInstance;
                    break;
                }
            }
        }

        if (isset($centerModel)) {
            if (!isset($centerModel->coords)) {
                throw new \InvalidArgumentException(
                    "Required center model " .
                    "{$centerModel->nameForDebug()} does not have coordinates, required by " .
                    "{$forModel->nameForDebug()}"
                );
            }
            return $centerModel;
        }

        if ($orFail) {
            $center = $centerType;
            if ($centerId) {
                $center .= " $centerId";
            }
            throw new \InvalidArgumentException("Case {$agencyCase->nameForDebug()}, model {$forModel->nameForDebug()} does not have required center [$center] for a location");
        }

        return null;
    }

    /**
     * @param CaseNoireModel|HasLocation $centerModel
     * @return Location
     * @throws \Exception
     */
    public static function getCenterLocation(CaseNoireModel $centerModel): Location
    {
        if (!isset($centerModel->location)) {
            throw new \InvalidArgumentException("Required center model [{$centerModel->nameForDebug()}] does not have a location");
        }
        if (!isset($centerModel->location->coords)) {
            throw new \InvalidArgumentException("Required center model [{$centerModel->nameForDebug()}] location does not have coordinates");
        }

        return $centerModel->location;
    }

    public static function getCoordsNextToLocation(Location $location): Point
    {
        return static::getRandomPoint($location->coords, 3, 1);
    }

    public static function get(
        Point $center,
        int $maxRangeMeters,
        int $minRangeMeters = 0,
        array $allowedTypes = [],
        array $notIds = []
    ): Location {
        // 1) Find known locations 1st
        $query = Location::inRange($center, $maxRangeMeters, $minRangeMeters);
        if ($notIds) {
            $query->whereNotIn('id', $notIds);
        }
        $existingLocations = $query->get();

        if ($existingLocations->count()) {
            return $existingLocations->random();
        }

        // 2) Try 10 times to find locations
        $attempts = 0;
        do {
            $locations = static::findLocations(
                $center,
                $maxRangeMeters,
                $minRangeMeters,
                $allowedTypes,
                $notIds
            );
            if ($locations->count()) {
                return $locations->random();
            }

            $attempts++;
        } while($attempts < 10);

        throw new \InvalidArgumentException("Could not find any locations within {$minRangeMeters}-{$maxRangeMeters}m of {$center->toJson()}");
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
            $existingLocation = $blueprint->findExistingModel();
            if ($existingLocation) {
                // existing location is not reserved -> use it
                if (!in_array($existingLocation->id, $reservedIds)) {
                    $locations[] = $existingLocation;
                }
                continue;
            }

            // no existing location -> create it
            $locations[] = Location::create($blueprint->getModelParams());
        }

        // Forget Locations that ended up outside of the range
        foreach ($locations as $i => $location) {
            $distance = static::getDistanceInMeters($center, $location);
            if ($distance < $minRange || $distance > $maxRange) {
                unset($locations[$i]);
            }
        }

        return collect($locations);
    }

    public static function getRandomPoint(Point $center, int $maxRangeMeters, int $minRangeMeters = 0): Point
    {
        $distance = rand($minRangeMeters, $maxRangeMeters);
        $bearing = deg2rad(rand(0, 359));
        $centerLat = deg2rad($center->getLat());
        $centerLng = deg2rad($center->getLng());

        $distanceDividedByEarthRadius = $distance / Location::$earthRadiusInMeters;

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

    /**
     * @param Location|Point $from
     * @param Location|Point $to
     * @return int
     */
    public static function getDistanceInMeters($from, $to): int
    {
        if (isset($from->coords)) {
            $from = $from->coords;
        }
        if (isset($to->coords)) {
            $to = $to->coords;
        }
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