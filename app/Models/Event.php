<?php

namespace App\Models;

use App\Models\Common\HasAndSpawnsInstances;
use App\Models\Common\CaseNoireModel;
use App\Models\Common\HasInstances;
use App\Models\Common\IsPartOfCase;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Event
 *
 * @property int $id
 * @property int $case_template_id
 * @property int|null $fired_by_event_id
 * @property string $name
 * @property string|null $description
 * @property string|null $image_url
 * @property int|null $timer
 * @property string $location_settings
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AgencyCase[] $agencyCases
 * @property-read int|null $agency_cases_count
 * @property-read \App\Models\CaseTemplate $caseTemplate
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Event[] $eventsToFire
 * @property-read int|null $events_to_fire_count
 * @property-read \App\Models\Event|null $firedByEvent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ModelInstance[] $instances
 * @property-read int|null $instances_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Location[] $locations
 * @property-read int|null $locations_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Event newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Event newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Event query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Event whereCaseTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Event whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Event whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Event whereFiredByEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Event whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Event whereImageUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Event whereLocationSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Event whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Event whereTimer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Event whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Event extends CaseNoireModel
{
    use IsPartOfCase, HasAndSpawnsInstances;

    protected $fillable = [
        'case_template_id',
        'fired_by_event_id',
        'name',
        'description',
        'image_url',
        'timer',
        'location_settings',
    ];

    public function firedByEvent(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'fired_by_event_id');
    }

    public function eventsToFire(): HasMany
    {
        return $this->hasMany(Event::class, 'fired_by_event_id');
    }
}
