<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mesa_entrada_firmante', function (Blueprint $table) {
            // Agregar la columna 'tipo' que permite valores null, además de 'FIRMANTE' o 'SOLICITANTE'
            $table->enum('tipo', ['FIRMANTE', 'SOLICITANTE'])->nullable()->after('id_firmante')->default('FIRMANTE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mesa_entrada_firmante', function (Blueprint $table) {
            $table->dropColumn('tipo');
        });
    }
};
