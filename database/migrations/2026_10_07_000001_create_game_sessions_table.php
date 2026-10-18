<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')
                ->constrained('activities', 'id')
                ->cascadeOnDelete();
            $table->string('code', 8)->unique();
            $table->enum('status', ['waiting', 'starting', 'playing', 'finished', 'cancelled'])->default('waiting');
            $table->unsignedTinyInteger('max_players')->default(4);
            $table->foreignId('created_by')->nullable()
                ->constrained('users', 'id')
                ->nullOnDelete();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();

            /*
             * El turno actual y el ganador se referencian por id sin
             * constraint de FK para evitar una dependencia circular
             * con participations (game_session_id -> participations).
             */
            $table->unsignedBigInteger('current_turn_participation_id')->nullable()->index();
            $table->enum('result_type', ['winner', 'draw', 'collaborative_success'])->nullable();
            $table->unsignedBigInteger('winner_participation_id')->nullable()->index();
            $table->json('state')->nullable();
            $table->timestamps();
            $table->index('activity_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_sessions');
    }
};
