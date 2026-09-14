<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proyectos_destinos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->timestamps();
        });

        $ahora = now();
        $destinos = DB::table('proyectos_en_estudio')
            ->whereNotNull('destino')
            ->where('destino', '!=', '')
            ->distinct()
            ->pluck('destino')
            ->map(fn ($nombre) => trim($nombre))
            ->filter()
            ->unique(fn ($nombre) => mb_strtolower($nombre))
            ->map(fn ($nombre) => [
                'nombre' => $nombre,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ])
            ->values()
            ->all();

        if ($destinos) {
            DB::table('proyectos_destinos')->insertOrIgnore($destinos);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('proyectos_destinos');
    }
};
