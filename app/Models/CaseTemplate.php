<?php

namespace App\Models;

use App\Models\Common\CaseNoireModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

/**
 * App\Models\CaseTemplate
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Collection|Clue[]|Conversation[]|Event[]|Evidence[]|Person[] $all_models
 * @property-read \Illuminate\Database\Eloquent\Collection|AgencyCase[] $agencyCases
 * @property-read int|null $agency_cases_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Clue[] $clues
 * @property-read int|null $clues_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Conversation[] $conversations
 * @property-read int|null $conversations_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Event[] $events
 * @property-read int|null $events_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Evidence[] $evidences
 * @property-read int|null $evidences_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Person[] $persons
 * @property-read int|null $persons_count
 * @method static \Illuminate\Database\Eloquent\Builder|CaseTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CaseTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CaseTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder|CaseTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CaseTemplate whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CaseTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CaseTemplate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CaseTemplate whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CaseTemplate whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CaseTemplate extends CaseNoireModel
{
    protected $fillable = [
        'name',
        'description',
        'type',
    ];

    public function agencyCases(): HasMany
    {
        return $this->hasMany(AgencyCase::class);
    }

    public function clues(): HasMany
    {
        return $this->hasMany(Clue::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function evidences(): HasMany
    {
        return $this->hasMany(Evidence::class);
    }

    public function persons(): HasMany
    {
        return $this->hasMany(Person::class);
    }

    public function getAllModelsAttribute(): Collection
    {
        return $this->clues
            ->merge($this->conversations)
            ->merge($this->events)
            ->merge($this->evidences)
            ->merge($this->persons);
    }

    public function delete()
    {
        if ($this->agencyCases()->exists() || $this->clues()->exists() ||
            $this->conversations()->exists() || $this->events()->exists() ||
            $this->evidences()->exists() || $this->persons()->exists()
        ) {
            throw new ConflictHttpException("CaseTemplate ID {$this->id} is active and cannot be deleted");
        }
        return parent::delete();
    }
}
