<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['kahoot_id','question','position','time_limit','score'])]
#[Hidden(['created_at','updated_at'])]
class Question extends Model
{
    public $timestamps = true;

    public function kahoot(): BelongsTo
    {
        return $this->belongsTo(Kahoot::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(Option::class);
    }
}
