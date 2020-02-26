<?php

namespace App\Models;

use App\Models\Common\CaseNoireModel;
use App\Models\Common\HasCoordinates;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Query\Expression;

/**
 * App\Models\Location
 *
 * @property int $id
 * @property string $hash
 * @property string $source
 * @property string $source_id
 * @property Point|null $coords
 * @property float|null $lat
 * @property float|null $lng
 * @property string $address
 * @property string|null $name
 * @property string|null $description
 * @property string|null $image_url
 * @property string|null $link
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AgencyCase[] $agencyCases
 * @property-read int|null $agency_cases_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Location comparison($geometryColumn, $geometry, $relationship)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Location contains($geometryColumn, $geometry)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Location crosses($geometryColumn, $geometry)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Location disjoint($geometryColumn, $geometry)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Location distance($geometryColumn, $geometry, $distance)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Location distanceExcludingSelf($geometryColumn, $geometry, $distance)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Location distanceSphere($geometryColumn, $geometry, $distance)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Location distanceSphereExcludingSelf($geometryColumn, $geometry, $distance)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Location distanceSphereValue($geometryColumn, $geometry)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Location distanceValue($geometryColumn, $geometry)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Location doesTouch($geometryColumn, $geometry)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Location equals($geometryColumn, $geometry)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Location intersects($geometryColumn, $geometry)
 * @method static \Grimzy\LaravelMysqlSpatial\Eloquent\Builder|\App\Models\Location newModelQuery()
 * @method static \Grimzy\LaravelMysqlSpatial\Eloquent\Builder|\App\Models\Location newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Location orderByDistance($geometryColumn, $geometry, $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Location orderByDistanceSphere($geometryColumn, $geometry, $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Location orderBySpatial($geometryColumn, $geometry, $orderFunction, $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Location overlaps($geometryColumn, $geometry)
 * @method static \Grimzy\LaravelMysqlSpatial\Eloquent\Builder|\App\Models\Location query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Location whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Location whereCoords($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Location whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Location whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Location whereHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Location whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Location whereImageUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Location whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Location whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Location whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Location whereSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Location whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Location within($geometryColumn, $polygon)
 * @mixin \Eloquent
 */
class Location extends CaseNoireModel
{
    use HasCoordinates;

    const SOURCE_MAPBOX = 'mapbox';
    const SOURCE_TEST = 'test';
    const SOURCES = [
        self::SOURCE_MAPBOX,
        self::SOURCE_TEST,
    ];

    const TYPE_ADDRESS = 'address';
    const TYPE_POI = 'poi';
    const TYPES = [
        self::TYPE_ADDRESS,
        self::TYPE_POI,
    ];

    protected $fillable = [
        'source',
        'source_id',
        'coords',
        'address',
        'name',
        'description',
        'image_url',
        'link',
        'hash', // overwritten on create/save
    ];

    public function save(array $options = [])
    {
        $this->hash = static::generateHash($this->coords, $this->address);
        return parent::save($options);
    }

    public static function generateHash(Point $coords, string $address): string
    {
        return md5("{$coords->getLat()},{$coords->getLng()}|{$address}");
    }

    public function agencyCases(): BelongsToMany
    {
        return $this->belongsToMany(AgencyCase::class, ModelInstance::table());
    }
}
