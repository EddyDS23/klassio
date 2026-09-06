<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['activity_id', 'student_id', 'team_id', 'attempt', 'status', 'score', 'completed_at', 'elapsed_seconds'])]
#[Hidden(['created_at', 'updated_at'])]
class Participation extends Model
{
    public $timestamps = true;

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function searchwordAnswers(): HasMany
    {
        return $this->hasMany(SearchwordAnswer::class);
    }

    public function crosswordAnswers(): HasMany{
        return $this->hasMany(CrosswordAnswer::class);
    }
}
