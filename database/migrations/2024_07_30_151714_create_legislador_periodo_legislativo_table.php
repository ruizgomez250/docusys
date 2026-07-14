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
        Schema::create('legislador_periodo_legislativo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('legislador_id')->constrained('legisladores')->onDelete('cascade');
            $table->foreignId('periodo_legislativo_id')->constrained('periodos_legislativos')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legislador_periodo_legislativo');
    }
};
