<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['wordsearch_id','word','row','column','direction','score'])]
#[Hidden(['created_at','updated_at'])]
class Word extends Model
{
    public $timestamps = true;

    public function wordsearch(): BelongsTo
    {
        return $this->belongsTo(Wordsearch::class);
    }
}
