<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['activity_id', 'rows', 'columns', 'grid'])]
#[Hidden(['created_at', 'updated_at'])]
class Wordsearch extends Model
{
    protected $table = 'worksearches';
    public $timestamps = true;

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function words(): HasMany
    {
        return $this->hasMany(Word::class);
    }

    protected function casts(): array
    {
        return [
            'grid' => 'array',
        ];
    }
}
