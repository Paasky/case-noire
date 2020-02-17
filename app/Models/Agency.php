<?php

namespace App\Models;

use App\Models\Common\CaseNoireModel;
use App\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Agency
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string|null $slogan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Agent[] $agents
 * @property-read int|null $agents_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AgencyCase[] $cases
 * @property-read int|null $cases_count
 * @property-read \App\User $owner
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agency newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agency newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agency query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agency whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agency whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agency whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agency whereSlogan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agency whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agency whereUserId($value)
 * @mixin \Eloquent
 */
class Agency extends CaseNoireModel
{
    protected $fillable = [
        'user_id',
        'name',
        'slogan',
    ];

    public function agents(): BelongsToMany
    {
        return $this->belongsToMany(Agent::class);
    }

    public function cases(): HasMany
    {
        return $this->hasMany(AgencyCase::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
