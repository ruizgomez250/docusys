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
        Schema::create('designaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('legislador_id')->constrained('legisladores')->onDelete('cascade');
            $table->foreignId('entidad_id')->constrained('entidades')->onDelete('cascade');
            $table->foreignId('cargo_id')->nullable()->constrained('cargos')->onDelete('cascade');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('designaciones');
    }
};
