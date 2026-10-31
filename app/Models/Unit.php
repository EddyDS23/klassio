<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['class_id', 'number', 'name'])]
#[Hidden(['created_at', 'updated_at'])]
class Unit extends Model
{
    public $timestamps = true;

    protected function casts(): array
    {
        return [
            'number' => 'integer',
        ];
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }
}
