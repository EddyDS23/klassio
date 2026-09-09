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
        Schema::create('crossword_answers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('participation_id')
                ->constrained('participations')
                ->cascadeOnDelete();

            $table->foreignId('crossword_word_id')
                ->constrained('crossword_words')
                ->cascadeOnDelete();

            $table->string('response');

            $table->boolean('is_correct')->default(false);

            $table->unsignedInteger('score')->default(0);

            $table->timestamp('answered_at')->useCurrent();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crossword_answers');
    }
};
