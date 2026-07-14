<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proyectos_configuracion', function (Blueprint $table) {
            $table->string('tipo_sesion')->default('EXTRAORDINARIA')->after('leyenda');
            $table->integer('nro_sesion')->default(1)->after('tipo_sesion');
        });
    }

    public function down(): void
    {
        Schema::table('proyectos_configuracion', function (Blueprint $table) {
            $table->dropColumn(['tipo_sesion', 'nro_sesion']);
        });
    }
};
