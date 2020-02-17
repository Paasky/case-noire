<?php

namespace App\Models;

use App\Models\Common\HasAndSpawnsInstances;
use App\Models\Common\CaseNoireModel;
use App\Models\Common\HasInstances;
use App\Models\Common\IsPartOfCase;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\Models\Clue
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $image_url
 * @property int $case_template_id
 * @property int $given_by_id
 * @property string $given_by_type
 * @property int|null $evidence_id
 * @property int|null $evidence_requirement_id
 * @property string|null $evidence_requirement_type
 * @property string $location_settings
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AgencyCase[] $agencyCases
 * @property-read int|null $agency_cases_count
 * @property-read \App\Models\CaseTemplate $caseTemplate
 * @property-read \App\Models\Evidence|null $evidence
 * @property-read \App\Models\Clue|null $evidenceRequirement
 * @property-read \App\Models\Clue $givenBy
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ModelInstance[] $instances
 * @property-read int|null $instances_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Location[] $locations
 * @property-read int|null $locations_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ClueGroup[] $requiredForClueGroups
 * @property-read int|null $required_for_clue_groups_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Clue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Clue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Clue query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Clue whereCaseTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Clue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Clue whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Clue whereEvidenceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Clue whereEvidenceRequirementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Clue whereEvidenceRequirementType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Clue whereGivenById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Clue whereGivenByType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Clue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Clue whereImageUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Clue whereLocationSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Clue whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Clue whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Clue extends CaseNoireModel
{
    use IsPartOfCase, HasAndSpawnsInstances;

    protected $fillable = [
        'name',
        'description',
        'image_url',
        'case_template_id',
        'given_by_id',
        'given_by_type',
        'evidence_id',
        'evidence_requirement_id',
        'evidence_requirement_type',
        'location_settings',
    ];

    public function givenBy(): MorphTo
    {
        return $this->morphTo();
    }

    public function evidence(): BelongsTo
    {
        return $this->belongsTo(Evidence::class);
    }

    public function evidenceRequirement(): MorphTo
    {
        return $this->morphTo();
    }

    public function requiredForClueGroups(): BelongsToMany
    {
        return $this->belongsToMany(ClueGroup::class);
    }
}
