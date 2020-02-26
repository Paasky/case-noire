<?php

namespace App\Models;

use App\Models\Common\CaseNoireModel;
use App\Models\Common\HasCoordinates;
use App\User;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\Agent
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string|null $slogan
 * @property Point|null $coords
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Agency[] $agencies
 * @property-read int|null $agencies_count
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent comparison($geometryColumn, $geometry, $relationship)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent contains($geometryColumn, $geometry)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent crosses($geometryColumn, $geometry)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent disjoint($geometryColumn, $geometry)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent distance($geometryColumn, $geometry, $distance)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent distanceExcludingSelf($geometryColumn, $geometry, $distance)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent distanceSphere($geometryColumn, $geometry, $distance)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent distanceSphereExcludingSelf($geometryColumn, $geometry, $distance)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent distanceSphereValue($geometryColumn, $geometry)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent distanceValue($geometryColumn, $geometry)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent doesTouch($geometryColumn, $geometry)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent equals($geometryColumn, $geometry)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent intersects($geometryColumn, $geometry)
 * @method static \Grimzy\LaravelMysqlSpatial\Eloquent\Builder|\App\Models\Agent newModelQuery()
 * @method static \Grimzy\LaravelMysqlSpatial\Eloquent\Builder|\App\Models\Agent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent orderByDistance($geometryColumn, $geometry, $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent orderByDistanceSphere($geometryColumn, $geometry, $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent orderBySpatial($geometryColumn, $geometry, $orderFunction, $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent overlaps($geometryColumn, $geometry)
 * @method static \Grimzy\LaravelMysqlSpatial\Eloquent\Builder|\App\Models\Agent query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent whereCoords($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent whereSlogan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent within($geometryColumn, $polygon)
 * @mixin \Eloquent
 */
class Agent extends CaseNoireModel
{
    use HasCoordinates;

    protected $fillable = [
        'user_id',
        'coords',
        'name',
        'slogan',
    ];

    public function agencies(): BelongsToMany
    {
        return $this->belongsToMany(Agency::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
