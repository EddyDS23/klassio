<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'participation_id',
    'question_id',
    'option_id',
    'is_correct',
    'score',
    'answered_at',
])]
class KahootAnswer extends Model
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

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(Option::class);
    }
}