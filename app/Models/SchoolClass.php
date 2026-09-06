<?php

namespace App\Models;

use Illuminate\Console\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['teacher_id','name','description','code','status'])]
#[Hidden(['created_at','updated_at'])]
class SchoolClass extends Model
{
 
    public $table = "classes";
    public $timestamps = true;

    public function teacher():BelongsTo{
        return $this->belongsTo(User::class,'teacher_id');
    }

    public function enrollments(): HasMany{
        return $this->hasMany(Enrollment::class,'class_id');
    }

    public function activities(): HasMany{
        return $this->hasMany(Activity::class,'class_id');
    }

}
