<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mesa_entrada_firmante', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_mentrada');
            $table->unsignedBigInteger('id_firmante');
            $table->timestamps();

            // Definimos las claves foráneas
            $table->foreign('id_mentrada')->references('id')->on('mesa_entrada')->onDelete('cascade');
            $table->foreign('id_firmante')->references('id')->on('firmantes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mesa_entrada_firmante');
    }
};
