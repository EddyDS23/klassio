<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['activity_id','rows','columns','grid'])]
#[Hidden(['created_at','updated_at'])]
class Crossword extends Model
{

    public $timestamps = true;
    
    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function words(): HasMany
    {
        return $this->hasMany(CrosswordWord::class);
    }

}
