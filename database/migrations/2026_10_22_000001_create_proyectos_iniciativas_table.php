<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proyectos_iniciativas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->timestamps();
        });

        $ahora = now();

        // Instituciones con faculty de iniciativa legislativa (Art. 203 - Constitución Nacional)
        $iniciativas = [
            'Senadores de la Nación',
            'Diputados de la Nación',
            'Poder Ejecutivo (a través del Presidente de la República)',
            'Corte Suprema de Justicia',
            'Ciudadanos (Iniciativa Popular)',
        ];

        $filas = array_map(fn ($nombre) => [
            'nombre' => $nombre,
            'created_at' => $ahora,
            'updated_at' => $ahora,
        ], $iniciativas);

        DB::table('proyectos_iniciativas')->insertOrIgnore($filas);
    }

    public function down(): void
    {
        Schema::dropIfExists('proyectos_iniciativas');
    }
};
