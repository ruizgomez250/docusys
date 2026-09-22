<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sesiones', function (Blueprint $table) {
            $table->dropUnique(['tipo_sesion', 'nro_sesion']);
            $table->index(['tipo_sesion', 'nro_sesion']);
        });
    }

    public function down(): void
    {
        // Restoring uniqueness fails safely if duplicate sessions still exist.
        Schema::table('sesiones', function (Blueprint $table) {
            $table->unique(['tipo_sesion', 'nro_sesion']);
            $table->dropIndex(['tipo_sesion', 'nro_sesion']);
        });
    }
};
