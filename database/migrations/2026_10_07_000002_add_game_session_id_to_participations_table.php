<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('participations', function (Blueprint $table) {
            $table->foreignId('game_session_id')
                ->nullable()
                ->after('id')
                ->constrained('game_sessions', 'id')
                ->nullOnDelete();
        });

        Schema::table('participations', function (Blueprint $table) {
            $table->enum('status', ['waiting', 'started', 'completed', 'abandoned', 'expired'])
                ->default('started')
                ->change();
        });

        Schema::table('participations', function (Blueprint $table) {
            $table->index('game_session_id');
        });
    }

    public function down(): void
    {
        Schema::table('participations', function (Blueprint $table) {
            $table->dropForeign(['game_session_id']);
            $table->dropIndex(['game_session_id']);
            $table->dropColumn('game_session_id');
        });

        Schema::table('participations', function (Blueprint $table) {
            $table->enum('status', ['started', 'completed', 'abandoned', 'expired'])
                ->default('started')
                ->change();
        });
    }
};
