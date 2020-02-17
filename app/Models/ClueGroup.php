<?php

namespace App\Models;

use App\Models\Common\CaseNoireModel;
use App\Models\Common\GivesClues;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\Models\ClueGroup
 *
 * @property int $id
 * @property int $gain_id
 * @property string $gain_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ClueGroup $gain
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Clue[] $givenClues
 * @property-read int|null $given_clues_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Clue[] $requiredClues
 * @property-read int|null $required_clues_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ClueGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ClueGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ClueGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ClueGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ClueGroup whereGainId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ClueGroup whereGainType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ClueGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ClueGroup whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ClueGroup extends CaseNoireModel
{
    use GivesClues;

    protected $fillable = [
        'gain_id',
        'gain_type',
    ];

    public function gain(): MorphTo
    {
        return $this->morphTo();
    }

    public function requiredClues(): BelongsToMany
    {
        return $this->belongsToMany(Clue::class);
    }
}
