<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['participation_id', 'matching_item_id', 'response', 'is_correct', 'score', 'answered_at'])]
#[Hidden(['created_at', 'updated_at'])]
class MatchingAnswer extends Model
{
    public $timestamps = true;

    public function participation(): BelongsTo
    {
        return $this->belongsTo(Participation::class);
    }

    public function matchingItem(): BelongsTo
    {
        return $this->belongsTo(MatchingItem::class);
    }
}