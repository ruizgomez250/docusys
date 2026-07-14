<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proyectos_en_estudio', function (Blueprint $table) {
            $table->foreignId('sesion_id')->nullable()->constrained('sesiones')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('proyectos_en_estudio', function (Blueprint $table) {
            $table->dropForeign(['sesion_id']);
            $table->dropColumn('sesion_id');
        });
    }
};
