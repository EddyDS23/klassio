<?php

namespace App\Models;

use App\Support\PeriodResolver;
use DateTimeInterface;
use Illuminate\Console\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['teacher_id', 'name', 'description', 'code', 'status'])]
#[Hidden(['created_at', 'updated_at'])]
class SchoolClass extends Model
{
    public $table = 'classes';

    public $timestamps = true;

    #[Scope]
    protected function periodOf(Builder $query, string $code): Builder
    {
        [$year, $half] = PeriodResolver::split($code) ?? [0, 1];

        return $query
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $half === 1 ? '<=' : '>=', $half === 1 ? 6 : 7);
    }

    public function period(?DateTimeInterface $date = null): string
    {
        return PeriodResolver::forDate($date ?? $this->created_at);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'class_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class, 'class_id');
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class, 'class_id')->orderBy('number');
    }
}
