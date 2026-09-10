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
        Schema::create('matching_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matching_id')->constrained('matchings','id')->cascadeOnDelete();
            $table->string('left_text');
            $table->string('right_text');
            $table->unsignedSmallInteger('score');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matching_items');
    }
};
