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
            $table->id('id');
            $table->foreignId('persona_id')->constrained('personas');
            $table->string('cargo')->default('Diputado Nacional');
            $table->foreignId('partido_id')->constrained('partidos_politicos');
            $table->foreignId('periodo_legislativo_id')->constrained('periodos_legislativos');
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
