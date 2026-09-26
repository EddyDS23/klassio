<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['roulette_id', 'question', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_option', 'points'])]
#[Hidden(['created_at', 'updated_at', 'correct_option'])]
class RouletteItem extends Model
{
    public $timestamps = true;

    public function roulette(): BelongsTo
    {
        return $this->belongsTo(Roulette::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(RouletteAnswer::class);
    }
}
