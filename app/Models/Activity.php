<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Override;

#[Fillable(['class_id','teacher_id','title','description','type','mode','max_score','time_limit','attempts','due_at','status'])]
#[Hidden(['created_at','updated_at'])]
class Activity extends Model
{
    public $timestamps = true;

    #[Override]
    protected function casts()
    {
        return [
            'due_at'=>'datetime',
        ];
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function wordsearch(): HasOne
    {
        return $this->hasOne(Wordsearch::class);
    }

    public function crossword(): HasOne
    {
        return $this->hasOne(Crossword::class);
    }

    public function matching(): HasOne
    {
        return $this->hasOne(Matching::class);
    }

    public function kahoot(): HasOne
    {
        return $this->hasOne(Kahoot::class);
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function participations(): HasMany
    {
        return $this->hasMany(Participation::class);
    }
}
