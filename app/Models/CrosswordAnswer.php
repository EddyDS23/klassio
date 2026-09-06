<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['participation_id','crossword_word_id','response','is_correct','score'])]
class CrosswordAnswer extends Model
{
    public $timestamps = true;

    public function participation(): BelongsTo { 
        return $this->belongsTo(Participation::class); 
    }

    public function crosswordWord(): BelongsTo { 
        return $this->belongsTo(CrosswordWord::class);
    }
}