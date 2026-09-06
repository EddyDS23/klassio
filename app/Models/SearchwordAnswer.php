<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'participation_id',
    'word_id',
    'score',
    'found_at',
])]
class SearchwordAnswer extends Model
{
    public function participation(): BelongsTo
    {
        return $this->belongsTo(Participation::class);
    }

    public function word(): BelongsTo
    {
        return $this->belongsTo(Word::class);
    }
}