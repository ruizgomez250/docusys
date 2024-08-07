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
        Schema::create('permisos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('legislador_id')->constrained('legisladores')->onDelete('cascade');
            $table->enum('tipo_permiso', ['Particular', 'Reposo Médico', 'Otro']);
            $table->date('fecha');
            $table->text('observacion')->nullable();
            $table->enum('estado', ['Vigente', 'Anulado'])->default('Vigente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permisos');
    }
};
