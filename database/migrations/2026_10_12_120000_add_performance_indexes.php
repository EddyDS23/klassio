<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Índices para los filtros más usados (admin + listados).
// Solo lectura: no cambia columnas ni datos.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index('role');
            $table->index('status');
        });

        Schema::table('classes', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->index(['class_id', 'status']);
            $table->index(['status', 'type']);
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropIndex(['class_id', 'status']);
            $table->dropIndex(['status', 'type']);
        });

        Schema::table('classes', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['status']);
        });
    }
};
