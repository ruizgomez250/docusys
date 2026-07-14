<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('recorrido_doc', function (Blueprint $table) {
        // Agregar el campo id_usuario, relacionándolo con la tabla users y definiendo un valor por defecto de 1
        $table->unsignedBigInteger('id_usuario')->default(1)->after('fecha');
        
        // Definir la clave foránea que se relaciona con la tabla users
        $table->foreign('id_usuario')->references('id')->on('users')->onDelete('cascade');
    });
}

/**
 * Reverse the migrations.
 *
 * @return void
 */
public function down()
{
    Schema::table('recorrido_doc', function (Blueprint $table) {
        // Eliminar la clave foránea y el campo id_usuario si se revierte la migración
        $table->dropForeign(['id_usuario']);
        $table->dropColumn('id_usuario');
    });
}

};
