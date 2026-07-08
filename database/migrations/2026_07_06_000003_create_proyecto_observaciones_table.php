<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('proyecto_observaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_proyecto')->constrained('proyectos_en_estudio')->onDelete('cascade');
            $table->integer('numero');
            $table->text('contenido')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('proyecto_observaciones');
    }
};
