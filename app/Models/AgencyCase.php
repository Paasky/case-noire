<?php

namespace App\Models;

use App\Models\Common\CaseNoireModel;
use App\Models\Common\CreatesInstances;
use App\Models\Common\HasLocation;
use App\Models\Common\IsPartOfCase;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\AgencyCase
 *
 * @property int $id
 * @property int $agency_id
 * @property int $case_template_id
 * @property int $location_id
 * @property string $status
 * @property string|null $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Agency $agency
 * @property-read \App\Models\CaseTemplate $caseTemplate
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Clue[] $clues
 * @property-read int|null $clues_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Conversation[] $conversations
 * @property-read int|null $conversations_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Event[] $events
 * @property-read int|null $events_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Evidence[] $evidences
 * @property-read int|null $evidences_count
 * @property-read \App\Models\Location $location
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Location[] $locations
 * @property-read int|null $locations_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ModelInstance[] $modelInstances
 * @property-read int|null $model_instances_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Person[] $persons
 * @property-read int|null $persons_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AgencyCase newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AgencyCase newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AgencyCase query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AgencyCase whereAgencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AgencyCase whereCaseTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AgencyCase whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AgencyCase whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AgencyCase whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AgencyCase whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AgencyCase whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AgencyCase whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AgencyCase extends CaseNoireModel
{
    use IsPartOfCase, CreatesInstances, HasLocation;

    protected $fillable = [
        'agency_id',
        'case_template_id',
        'location_id',
        'status',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function nameForDebug(): string
    {
        $name = $this->caseTemplate->name ?? '´CaseTemplate missing´';
        return "$name [ID $this->id]";
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }
}
