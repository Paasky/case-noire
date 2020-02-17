<?php

namespace App\Models;

use App\Models\Common\CaseNoireModel;
use App\Models\Common\HasInstances;
use App\Models\Common\IsPartOfCase;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Conversation
 *
 * @property int $id
 * @property int $case_template_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AgencyCase[] $agencyCases
 * @property-read int|null $agency_cases_count
 * @property-read \App\Models\CaseTemplate $caseTemplate
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ModelInstance[] $instances
 * @property-read int|null $instances_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ConversationLine[] $lines
 * @property-read int|null $lines_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation whereCaseTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Conversation extends CaseNoireModel
{
    use IsPartOfCase, HasInstances;

    protected $fillable = [
        'case_template_id',
    ];

    public function lines(): HasMany
    {
        return $this->hasMany(ConversationLine::class);
    }
}
