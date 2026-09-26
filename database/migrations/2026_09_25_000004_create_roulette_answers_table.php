<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roulette_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participation_id')
                ->constrained('participations')
                ->cascadeOnDelete();
            $table->foreignId('roulette_item_id')
                ->constrained('roulette_items')
                ->cascadeOnDelete();
            $table->string('response');
            $table->boolean('is_correct')->default(false);
            $table->unsignedSmallInteger('score')->default(0);
            $table->timestamp('answered_at')->useCurrent();
            $table->timestamps();
            $table->index('participation_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roulette_answers');
    }
};
