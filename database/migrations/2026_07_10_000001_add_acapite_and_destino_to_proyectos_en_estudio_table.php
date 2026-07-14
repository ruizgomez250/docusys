<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proyectos_en_estudio', function (Blueprint $table) {
            $table->string('acapite')->nullable()->after('camara');
            $table->string('destino')->nullable()->after('acapite');
        });
    }

    public function down(): void
    {
        Schema::table('proyectos_en_estudio', function (Blueprint $table) {
            $table->dropColumn(['acapite', 'destino']);
        });
    }
};
