<?php

namespace App\Models;

use App\Models\Common\HasAndSpawnsInstances;
use App\Models\Common\CaseNoireModel;
use App\Models\Common\HasInstances;
use App\Models\Common\IsPartOfCase;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Person
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
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ConversationLine[] $conversationLines
 * @property-read int|null $conversation_lines_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ModelInstance[] $instances
 * @property-read int|null $instances_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Location[] $locations
 * @property-read int|null $locations_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person whereCaseTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person whereImageUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person whereLocationSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Person extends CaseNoireModel
{
    use IsPartOfCase, HasAndSpawnsInstances;

    protected $table = 'persons';

    protected $fillable = [
        'case_template_id',
        'name',
        'description',
        'image_url',
        'location_settings',
    ];

    public function conversationLines(): HasMany
    {
        return $this->hasMany(ConversationLine::class, 'said_by');
    }
}
