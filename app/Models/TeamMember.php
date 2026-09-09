<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['team_id','student_id'])]
#[Hidden(['created_at','updated_at'])]
class TeamMember extends Model
{
    public $timestamps = true;

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}