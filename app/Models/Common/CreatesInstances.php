<?php
/** @noinspection PhpUnused */

namespace App\Models\Common;

use App\Models\Clue;
use App\Models\Conversation;
use App\Models\Event;
use App\Models\Evidence;
use App\Models\ModelInstance;
use App\Models\Person;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @mixin CaseNoireModel
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

    public function persons(): MorphToMany
    {
        return $this->morphedByMany(Person::class, 'model', ModelInstance::table());
    }
}