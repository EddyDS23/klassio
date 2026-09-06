<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['class_id','student_id','status'])]
#[Hidden(['created_at','updated_at'])]
class Enrollment extends Model
{
    public $timestamps = true;

    public function class(): BelongsTo{
        return $this->belongsTo(SchoolClass::class);
    }

    public function student(): BelongsTo{
        return $this->belongsTo(User::class,'student_id');
    }

}
