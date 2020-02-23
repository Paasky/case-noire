<?php
/** @noinspection PhpUnused */

namespace App\Models\Common;

use App\Models\AgencyCase;
use App\Models\Location;
use App\Models\ModelInstance;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Query\Expression;

/**
 * @mixin CaseNoireModel
 * @property Point|null $coords
 * @property-read float|null $lat
 * @property-read float|null $lng
 */
trait HasCoordinates
{
    use SpatialTrait;

    public static $EARTH_RADIUS_IN_M = 6371000;

    public $spatialFields = [
        'coords',
    ];

    private function setLatLngOnSave(): void
    {
        $this->attributes['lat'] = $this->coords ? $this->coords->getLat() : null;
        $this->attributes['lng'] = $this->coords ? $this->coords->getLng() : null;
    }

    public static function inRange(Point $center, int $maxRangeMeters, int $minRangeMeters = 0, Builder &$query = null): Builder
    {
        $query = $query ?: static::query();

        $query->whereRaw(static::radiusExpression($center, $maxRangeMeters, $minRangeMeters));

        return $query;
    }

    public static function radiusExpression(Point $center, float $maxRadiusM, float $minRadiusM = null): Expression
    {
        $earthRadiusM = (int) self::$EARTH_RADIUS_IN_M;
        $centerLat = (float) $center->getLat();
        $centerLng = (float) $center->getLng();

        if (!$maxRadiusM) {
            throw new \InvalidArgumentException("Must give max radius for radius expression");
        }
        if ($minRadiusM) {
            $distanceLimit = "between $minRadiusM and $maxRadiusM";
        } else {
            $distanceLimit = "<= $maxRadiusM";
        }

        // Generate bounding box to increase performance
        $minLat = $centerLat - rad2deg($maxRadiusM / $earthRadiusM);
        $maxLat = $centerLat + rad2deg($maxRadiusM / $earthRadiusM);
        $minLng = $centerLng - rad2deg(asin($maxRadiusM / $earthRadiusM) / cos(deg2rad($centerLat)));
        $maxLng = $centerLng + rad2deg(asin($maxRadiusM / $earthRadiusM) / cos(deg2rad($centerLat)));

        return \DB::raw(
        // Bounding box
        "lat between $minLat and $maxLat and lng between $minLng and $maxLng and " .

        // Radius
        "$earthRadiusM * acos(" .
            "cos(radians($centerLat)) * cos(radians(lat)) * cos(radians(lng) - radians($centerLng)) +" .
            "sin(radians($centerLat)) * sin(radians(lat))" .
        ") $distanceLimit");
    }

    public function setLatAttribute($lat): void
    {
        throw new \BadFunctionCallException("lat-attribute is read-only. Set coords to update lat-lng values");
    }

    public function setLngAttribute($lng): void
    {
        throw new \BadFunctionCallException("lng-attribute is read-only. Set coords to update lat-lng values");
    }
}