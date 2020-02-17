<?php

namespace App\Models\Common;

use App\Models\CaseTemplate;
use App\Models\Clue;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @mixin CaseNoireModel
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Clue[] $givenClues
 * @property-read int|null $given_clues_count
 */
trait GivesClues
{
    public function givenClues(): MorphMany
    {
        return $this->morphMany(Clue::class, 'given_by');
    }
}