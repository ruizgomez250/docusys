<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('mesa_entrada', function (Blueprint $table) {
            $table->boolean('modificar')->default(0); // Agrega el campo 'modificar' con valor por defecto 0
        });
    }

    public function down()
    {
        Schema::table('mesa_entrada', function (Blueprint $table) {
            $table->dropColumn('modificar'); // Elimina el campo en caso de rollback
        });
    }
};
