<?php
/** @noinspection PhpUnused */

namespace App\Models\Common;

use App\Models\Clue;
use App\Models\Conversation;
use App\Models\Event;
use App\Models\Evidence;
use App\Models\Location;
use App\Models\ModelInstance;
use App\Models\Person;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @mixin CaseNoireModel
 * @property-read \Illuminate\Database\Eloquent\Collection|ModelInstance[] $modelInstances
 * @property-read int|null $model_instances_count
 */
trait CreatesInstances
{
    public function modelInstances(): HasMany
    {
        return $this->hasMany(ModelInstance::class);
    }

    public function clues(): MorphToMany
    {
        return $this->morphedByMany(Clue::class, 'model', ModelInstance::table());
    }

    public function conversations(): MorphToMany
    {
        return $this->morphedByMany(Conversation::class, 'model', ModelInstance::table());
    }

    public function events(): MorphToMany
    {
        return $this->morphedByMany(Event::class, 'model', ModelInstance::table());
    }

    public function evidences(): MorphToMany
    {
        return $this->morphedByMany(Evidence::class, 'model', ModelInstance::table());
    }

    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class, ModelInstance::table());
    }

    public function persons(): MorphToMany
    {
        return $this->morphedByMany(Person::class, 'model', ModelInstance::table());
    }

    public function getInstanceOf(CaseNoireModel $model, bool $orFail = false): ?ModelInstance
    {
        $modelType = get_class($model);
        foreach ($this->modelInstances as $modelInstance) {
            if ($modelInstance->model_id == $model->id && $modelInstance->model_type == $modelType) {
                return $modelInstance;
            }
        }

        if ($orFail) {
            throw new ModelNotFoundException("AgencyCase {$this->nameForDebug()} does not have an instance of required {$modelType} {$model->nameForDebug()}");
        }

        return null;
    }

    public function setInstanceOf(
        CaseNoireModel $model,
        Location $location = null,
        array $data = null,
        string $status = null
    ) {
        $attributes = [
            'location_id' => $location->id ?? null,
            'data' => $data,
            'status' => $status,
        ];
        switch (get_class($model)) {
            case Clue::class:
                $this->clues()->save($model, $attributes);
                break;
            case Conversation::class:
                $this->conversations()->save($model, $attributes);
                break;
            case Event::class:
                $this->events()->save($model, $attributes);
                break;
            case Evidence::class:
                $this->evidences()->save($model, $attributes);
                break;
            case Person::class:
                $this->persons()->save($model, $attributes);
                break;
        }
    }
}