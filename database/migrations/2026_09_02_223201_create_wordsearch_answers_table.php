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
        Schema::create('wordsearch_answers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('participation_id')
                ->constrained('participations')
                ->cascadeOnDelete();

            $table->foreignId('word_id')
                ->constrained('words')
                ->cascadeOnDelete();

            $table->unsignedInteger('score')->default(0);

            $table->timestamp('found_at')->useCurrent();

            $table->timestamps();

            $table->unique(['participation_id', 'word_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wordsearch_answers');
    }
};
