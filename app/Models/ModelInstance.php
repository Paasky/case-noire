<?php

namespace App\Models;

use App\Models\Common\CaseNoireModel;
use App\Models\Common\HasCoordinates;
use App\Models\Common\HasInstances;
use App\Models\Common\HasLocation;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\Models\ModelInstance
 *
 * @property int $id
 * @property int $agency_case_id
 * @property int|null $location_id
 * @property int $model_id
 * @property string $model_type
 * @property string|null $status
 * @property string|null $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AgencyCase $agencyCase
 * @property-read \App\Models\Location|null $location
 * @property-read CaseNoireModel|HasInstances $model
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ModelInstance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ModelInstance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ModelInstance query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ModelInstance whereAgencyCaseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ModelInstance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ModelInstance whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ModelInstance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ModelInstance whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ModelInstance whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ModelInstance whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ModelInstance whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ModelInstance whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ModelInstance extends CaseNoireModel
{
    use HasLocation, HasCoordinates;

    protected $fillable = [
        'agency_case_id',
        'location_id',
        'model_id',
        'model_type',
        'coords',
        'status',
        'data',
    ];

    public $spatialFields = [
        'coords',
    ];

    public function nameForDebug(): string
    {
        return "ModelInstance [ID $this->id] (of {$this->model->nameForDebug()})";
    }

    public function agencyCase(): BelongsTo
    {
        return $this->belongsTo(AgencyCase::class);
    }

    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
