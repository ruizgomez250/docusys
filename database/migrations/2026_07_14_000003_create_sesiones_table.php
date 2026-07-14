<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sesiones', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_sesion');
            $table->string('tipo_sesion')->default('EXTRAORDINARIA');
            $table->integer('nro_sesion');
            $table->timestamps();

            $table->unique(['tipo_sesion', 'nro_sesion']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesiones');
    }
};
