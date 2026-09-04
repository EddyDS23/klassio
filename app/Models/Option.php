<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

#[Fillable(['question_id','text','is_correct','position',])]
#[Hidden(['created_at','updated_at'])]
class Option extends Model
{
    public $timestamps = true;

    #[Override]
    protected function casts()
    {
        return [
            'is_correct'=>'boolean'
        ];
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
