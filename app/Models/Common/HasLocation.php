<?php
/** @noinspection PhpUnused */

namespace App\Models\Common;

use App\Models\AgencyCase;
use App\Models\Location;
use App\Models\ModelInstance;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @mixin CaseNoireModel
 * @property int|null $location_id
 * @property-read Location|null $location
 */
trait HasLocation
{
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}