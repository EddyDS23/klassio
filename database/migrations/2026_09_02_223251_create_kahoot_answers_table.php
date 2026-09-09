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
        Schema::create('kahoot_answers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('participation_id')
                ->constrained('participations')
                ->cascadeOnDelete();

            $table->foreignId('question_id')
                ->constrained('questions')
                ->cascadeOnDelete();

            $table->foreignId('option_id')
                ->constrained('options')
                ->cascadeOnDelete();

            $table->boolean('is_correct')->default(false);

            $table->unsignedSmallInteger('score')->default(0);

            $table->timestamp('answered_at')->useCurrent();

            $table->timestamps();

            $table->unique(['participation_id', 'question_id']);
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kahoot_answers');
    }
};
