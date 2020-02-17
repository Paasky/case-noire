<?php

namespace App\Models;

use App\Models\Common\CaseNoireModel;
use App\Models\Common\GivesClues;
use App\Models\Common\HasInstances;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\ConversationLine
 *
 * @property int $id
 * @property int $conversation_id
 * @property int|null $from_line_id
 * @property \Person|string $said_by
 * @property string $text
 * @property string|null $audio_file
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AgencyCase[] $agencyCases
 * @property-read int|null $agency_cases_count
 * @property-read \App\Models\Conversation $conversation
 * @property-read \App\Models\ConversationLine|null $fromLine
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Clue[] $givenClues
 * @property-read int|null $given_clues_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ModelInstance[] $instances
 * @property-read int|null $instances_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ConversationLine[] $nextLines
 * @property-read int|null $next_lines_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ConversationLine newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ConversationLine newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ConversationLine query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ConversationLine whereAudioFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ConversationLine whereConversationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ConversationLine whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ConversationLine whereFromLineId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ConversationLine whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ConversationLine whereSaidBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ConversationLine whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ConversationLine whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ConversationLine extends CaseNoireModel
{
    use HasInstances, GivesClues;

    protected $fillable = [
        'conversation_id',
        'from_line_id',
        'said_by',
        'text',
        'audio_file',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function fromLine(): BelongsTo
    {
        return $this->belongsTo(ConversationLine::class, 'from_line_id');
    }

    public function nextLines(): HasMany
    {
        return $this->hasMany(ConversationLine::class, 'from_line_id');
    }

    /**
     * @return Person|string
     */
    public function getSaidByAttribute()
    {
        return is_numeric($this->attributes['said_by']) ?
            Person::findOrFail($this->attributes['said_by']) :
            $this->attributes['said_by'];
    }
}
