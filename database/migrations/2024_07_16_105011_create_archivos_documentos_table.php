<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('archivos_documentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_mentrada');
            $table->string('nombre_archivo');
            $table->string('ruta_archivo');
            $table->timestamps();

            // Clave foránea
            $table->foreign('id_mentrada')->references('id')->on('mesa_entrada')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('archivos_documentos');
    }
};
