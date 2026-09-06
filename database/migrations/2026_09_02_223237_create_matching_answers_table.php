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
        Schema::create('matching_answers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('participation_id')
                ->constrained('participations')
                ->cascadeOnDelete();

            $table->foreignId('matching_item_id')
                ->constrained('matching_items')
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
        Schema::dropIfExists('matching_answers');
    }
};
