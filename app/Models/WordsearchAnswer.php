<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['participation_id', 'word_id', 'score', 'found_at'])]
#[Hidden(['created_at', 'updated_at'])]
class WordsearchAnswer extends Model
{
    public $timestamps = true;

    public function participation(): BelongsTo
    {
        return $this->belongsTo(Participation::class);
    }

    public function word(): BelongsTo
    {
        return $this->belongsTo(Word::class);
    }
}