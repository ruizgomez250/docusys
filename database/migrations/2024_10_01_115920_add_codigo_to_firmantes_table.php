<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('firmantes', function (Blueprint $table) {
            // Agregar el campo 'codigo' con un valor por defecto de 0
            $table->integer('codigo')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('firmantes', function (Blueprint $table) {
            // Eliminar el campo 'codigo' en caso de rollback
            $table->dropColumn('codigo');
        });
    }
};
