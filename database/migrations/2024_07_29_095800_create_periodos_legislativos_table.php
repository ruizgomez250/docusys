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
        Schema::create('periodos_legislativos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Nombre del periodo legislativo (por ejemplo, "2024-2025")
            $table->date('inicio');
            $table->date('fin');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periodos_legislativos');
    }
};
