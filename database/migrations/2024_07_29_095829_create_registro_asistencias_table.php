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
            $table->foreignId('legislador_id')->constrained('legisladores')->onDelete('cascade');
            $table->enum('sesion_tipo', ['Sesión Ordinaria', 'Sesión Extraordinaria', 'Otro']); // Enum para el tipo de sesión
            $table->date('fecha_sesion');
            $table->enum('estado', ['Presente', 'Ausente', 'Justificado']);
            $table->text('justificacion')->nullable(); // Opcional, solo si el estado es 'Justificado'
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
