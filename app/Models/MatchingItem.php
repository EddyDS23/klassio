<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['matching_id','left_text','right_text','score'])]
#[Hidden(['created_at','updated_at'])]
class MatchingItem extends Model
{
    public $timestamps = true;

     public function matching(): BelongsTo
    {
        return $this->belongsTo(Matching::class);
    }
}
