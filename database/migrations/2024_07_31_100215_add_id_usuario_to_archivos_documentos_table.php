<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('archivos_documentos', function (Blueprint $table) {
            $table->unsignedBigInteger('id_usuario')->default(1)->after('link'); // Añade la columna id_usuario con valor predeterminado 1
            $table->foreign('id_usuario')->references('id')->on('users')->onDelete('cascade'); // Define la clave foránea
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('archivos_documentos', function (Blueprint $table) {
            $table->dropForeign(['id_usuario']); // Eliminar la clave foránea
            $table->dropColumn('id_usuario'); // Eliminar la columna
        });
    }
};
