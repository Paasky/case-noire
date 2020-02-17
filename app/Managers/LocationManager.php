<?php

namespace App\Managers;

use App\Locations\Geolocator;
use App\Models\AgencyCase;
use App\Models\CaseTemplate;
use App\Models\Common\CaseNoireModel;
use App\Models\Common\HasAndSpawnsInstances;
use App\Models\Common\HasLocation;
use App\Models\Location;
use Grimzy\LaravelMysqlSpatial\Types\Point;

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
        $reservedIds = $agencyCase->locations->pluck('id')->add($agencyCase->location_id);

        $locationSettings = $forModel->location_settings;
        $centerType = $locationSettings->getSpawnCenterAtType();
        $centerName = $locationSettings->getSpawnCenterAtTypeName();

        if ($centerType == AgencyCase::class) {
            $centerModel = $agencyCase;
        } else {
            foreach ($agencyCase->modelInstances->where('model_type', $centerType) as $modelInstance) {
                if (!$centerName) {
                    $centerModel = $modelInstance->model;
                    break;
                }
                if ($centerName == $modelInstance->model->name ?? null) {
                    $centerModel = $modelInstance->model;
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
        } else {
            /** @var HasLocation $centerModel */
            if (!isset($centerModel->location)) {
                throw new \InvalidArgumentException("Required center model [{$centerModel->nameForDebug()}] does not have a location");
            }
        }

        $possibleLocations = Location
            ::inRange(
                $centerModel->location->coords,
                $locationSettings->getMaxRange(),
                $locationSettings->getMinRange()
            )
            ->whereNotIn('id', $reservedIds)
            ->get();

        if ($possibleLocations->count()) {
            return $possibleLocations->random();
        }

        $attempts = 0;
        $gelocator = new Geolocator();
        do {
            $blueprints = $gelocator->findInRange(
                $centerModel->location->coords,
                $locationSettings->getMaxRange(),
                $locationSettings->getMinRange(),
                $locationSettings->getAllowedTypes()
            );

            foreach ($blueprints as $blueprint) {
                $existingLocation = $blueprint->getExistingModel();
                if ($existingLocation) {
                    // existing location is not reserved -> use it
                    if (!in_array($existingLocation->id, $reservedIds->all())) {
                        return $existingLocation;
                    }
                    // it's already reserved -> continue to the next found location
                    continue;
                }

                // no existing location -> create it
                /** @noinspection PhpIncompatibleReturnTypeInspection */
                return Location::create($blueprint->getModelParams());
            }
            $attempts++;
        } while($attempts < 10);

        if (isset($existingLocation)) {
            return $existingLocation;
        }
        throw new \Exception("Could not find any locations for Agency Case [{$agencyCase->nameForDebug()}], for model [{$forModel->nameForDebug()}]");
    }

    public static function getRandomPoint(Point $center, int $maxDistMeters, int $minDistMeters = 0): Point
    {
        $distance = rand($minDistMeters, $maxDistMeters);
        $angle = rand(0, 359);
        $bearing = deg2rad($angle);
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