<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['activity_id'])]
#[Hidden(['created_at','updated_at'])]
class Matching extends Model
{

    public $timestamps = true;

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(MatchingItem::class);
    }
}
