<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes','id')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users','id')->cascadeOnDelete();
            $table->enum('status',['active','inactive'])->default('active');
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamps();
            $table->unique(['class_id','student_id']);
            $table->index('student_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
