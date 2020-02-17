<?php
/** @noinspection PhpUnused */

namespace App\Models\Common;

use App\Models\AgencyCase;
use App\Models\ModelInstance;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @mixin CaseNoireModel
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AgencyCase[] $agencyCases
 * @property-read int|null $agency_cases_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ModelInstance[] $instances
 * @property-read int|null $instances_count
 */
trait HasInstances
{
    public function agencyCases(): MorphToMany
    {
        return $this->morphToMany(AgencyCase::class, 'model', ModelInstance::table());
    }

    public function instances(): MorphMany
    {
        return $this->morphMany(ModelInstance::class, 'model');
    }
}