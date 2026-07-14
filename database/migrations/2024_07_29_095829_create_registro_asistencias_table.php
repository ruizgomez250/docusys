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
        Schema::create('registro_asistencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('legislador_id')->constrained('legisladores');
            $table->foreignId('sesion_id')->constrained('tipos_sesiones');
            $table->date('fecha_sesion'); // Añadir la columna
            $table->enum('estado', ['Presente', 'Ausente', 'Justificado']); // Presente, Ausente, Justificado
            $table->text('justificacion')->nullable(); // Opcional, solo si el estado es 'justificado'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registro_asistencias');
    }
};
