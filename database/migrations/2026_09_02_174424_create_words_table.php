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
        Schema::create('words', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wordsearch_id')->constrained('worksearches', 'id')->cascadeOnDelete();
            $table->string('word');
            $table->unsignedTinyInteger('row');
            $table->unsignedTinyInteger('column');
            $table->enum('direction', [
                'horizontal',
                'vertical',
                'diagonal',
                'horizontal_reverse',
                'vertical_reverse',
                'diagonal_reverse',
                'diagonal_alt',
                'diagonal_alt_reverse',
            ]);
            $table->unsignedSmallInteger('score');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('words');
    }
};
