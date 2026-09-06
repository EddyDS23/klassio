<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('participations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('activities', 'id')->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('users', 'id')->cascadeOnDelete();
            $table->foreignId('team_id')->nullable()->constrained('teams', 'id')->cascadeOnDelete();
            $table->unsignedTinyInteger('attempt')->default(1);
            $table->enum('status', ['started', 'completed', 'abandoned', 'expired'])->default('started');
            $table->unsignedSmallInteger('score')->default(0);
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedInteger('elapsed_seconds')->nullable();
            $table->timestamps();
            $table->index('activity_id');
            $table->index('student_id');
            $table->index('team_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participations');
    }
};
