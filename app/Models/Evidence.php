<?php

namespace App\Models;

use App\Models\Common\HasAndSpawnsInstances;
use App\Models\Common\CaseNoireModel;
use App\Models\Common\HasInstances;
use App\Models\Common\IsPartOfCase;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

/**
 * App\Models\Evidence
 *
 * @property int $id
 * @property int $case_template_id
 * @property string $name
 * @property string|null $description
 * @property string|null $image_url
 * @property string $location_settings
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AgencyCase[] $agencyCases
 * @property-read int|null $agency_cases_count
 * @property-read \App\Models\CaseTemplate $caseTemplate
 * @property-read mixed $given_by_classes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ClueGroup[] $givenByClueGroups
 * @property-read int|null $given_by_clue_groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Clue[] $givenByClues
 * @property-read int|null $given_by_clues_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ModelInstance[] $instances
 * @property-read int|null $instances_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Location[] $locations
 * @property-read int|null $locations_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Evidence newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Evidence newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Evidence query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Evidence whereCaseTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Evidence whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Evidence whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Evidence whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Evidence whereImageUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Evidence whereLocationSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Evidence whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Evidence whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Evidence extends CaseNoireModel
{
    use IsPartOfCase, HasAndSpawnsInstances;

    protected $table = 'evidences';

    protected $fillable = [
        'case_template_id',
        'name',
        'description',
        'image_url',
        'location_settings',
    ];

    public function givenByClues(): HasMany
    {
        return $this->hasMany(Clue::class);
    }

    public function givenByClueGroups(): MorphMany
    {
        return $this->morphMany(ClueGroup::class, 'gain');
    }

    public function getGivenByClassesAttribute(): Collection
    {
        return $this->givenByClues->merge($this->givenByClueGroups);
    }
}
