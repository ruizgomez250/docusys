<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('proyectos_configuracion', function (Blueprint $table) {
            $table->integer('ultimo_nro_expediente')->nullable()->after('leyenda');
        });

        DB::table('proyectos_configuracion')->update(['ultimo_nro_expediente' => 0]);
    }

    public function down()
    {
        Schema::table('proyectos_configuracion', function (Blueprint $table) {
            $table->dropColumn('ultimo_nro_expediente');
        });
    }
};
