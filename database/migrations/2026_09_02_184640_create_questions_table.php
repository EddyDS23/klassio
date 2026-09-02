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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kahoot_id')->constrained('kahoots','id')->cascadeOnDelete();
            $table->string('question');
            $table->unsignedTinyInteger('position');
            $table->unsignedInteger('time_limit');
            $table->unsignedInteger('score');
            $table->timestamps();
            $table->unique(['kahoot_id', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
