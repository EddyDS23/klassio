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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes', 'id')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users', 'id')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['word_search', 'crossword', 'matching', 'kahoot']);
            $table->enum('mode', ['individual', 'team'])->default('individual');
            $table->unsignedSmallInteger('max_score');
            $table->unsignedInteger('time_limit');
            $table->unsignedInteger('attempts')->nullable();
            $table->datetime('due_at')->nullable();
            $table->enum('status', ['draft', 'published', 'closed'])->default('draft');
            $table->timestamps();
            $table->index('type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
