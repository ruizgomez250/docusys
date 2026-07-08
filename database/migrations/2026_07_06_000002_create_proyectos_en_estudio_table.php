<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('proyectos_en_estudio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_mentrada')->constrained('mesa_entrada')->onDelete('cascade');
            $table->integer('nro_expediente');
            $table->year('anho');
            $table->text('presentado_por')->nullable();
            $table->string('fecha_recepcion_texto')->nullable();
            $table->string('membrete')->nullable();
            $table->text('leyenda')->nullable();
            $table->integer('cantidad_observaciones')->default(1);
            $table->text('contenido')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('proyectos_en_estudio');
    }
};
