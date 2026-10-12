<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * En multijugador un jugador que no responde a tiempo queda
     * registrado como fallo, por lo que option_id debe permitir NULL.
     */
    public function up(): void
    {
        Schema::table('kahoot_answers', function (Blueprint $table) {
            $table->foreignId('option_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('kahoot_answers', function (Blueprint $table) {
            $table->foreignId('option_id')->nullable(false)->change();
        });
    }
};
