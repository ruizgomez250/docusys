<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('legisladores', function (Blueprint $table) {
            $table->id();
            $table->string('ci')->unique()->nullable();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('apodo')->nullable();
            $table->string('circunscripcion')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->unique()->nullable();
            $table->date('fecha_nac')->nullable();
            $table->string('cargo')->default('Diputado Nacional');
            $table->foreignId('partido_id')->constrained('partidos_politicos');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legisladores');
    }
};
