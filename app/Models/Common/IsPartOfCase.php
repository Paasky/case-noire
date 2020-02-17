<?php

namespace App\Models\Common;

use App\Models\CaseTemplate;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin CaseNoireModel
 * @property int $case_template_id
 * @property-read CaseTemplate $caseTemplate
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AgencyCase whereCaseTemplateId($value)
 */
trait IsPartOfCase
{
    public function caseTemplate(): BelongsTo
    {
        return $this->belongsTo(CaseTemplate::class);
    }
}