<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mapa_recorrido', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_mentrada');
            $table->foreign('id_mentrada')->references('id')->on('mesa_entrada');
            $table->dateTime('fecha_recepcion');
            $table->unsignedBigInteger('id_actual')->nullable();
            $table->foreign('id_actual')->references('id')->on('destinos');
            $table->unsignedBigInteger('id_destino')->nullable();
            $table->foreign('id_destino')->references('id')->on('destinos');
            $table->text('observacion')->nullable();
            $table->boolean('estado')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mapa_recorrido');
    }
};
