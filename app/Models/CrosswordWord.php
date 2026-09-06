<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['crossword_id','word','clue','row','column','direction','score'])]
#[Hidden(['created_at','updated_at'])]
class CrosswordWord extends Model
{
    public $timestamps = true;

    public function crossword(): BelongsTo
    {
        return $this->belongsTo(Crossword::class);
    }
}
