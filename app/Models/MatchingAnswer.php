<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'participation_id',
    'matching_item_id',
    'response',
    'is_correct',
    'score',
    'answered_at',
])]
class MatchingAnswer extends Model
{
    public $timestamps = true;

    protected function casts(): array
    {
        return [
            'is_correct'  => 'boolean',
            'answered_at' => 'datetime',
        ];
    }

    public function participation(): BelongsTo
    {
        return $this->belongsTo(Participation::class);
    }

    public function matchingItem(): BelongsTo
    {
        return $this->belongsTo(MatchingItem::class);
    }
}