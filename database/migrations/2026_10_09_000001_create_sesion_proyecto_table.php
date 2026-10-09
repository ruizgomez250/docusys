<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sesion_proyecto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesion_id')->constrained('sesiones')->cascadeOnDelete();
            $table->foreignId('proyecto_id')->constrained('proyectos_en_estudio')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['sesion_id', 'proyecto_id']);
        });

        $ahora = now();
        DB::table('proyectos_en_estudio')
            ->whereNotNull('sesion_id')
            ->select(['id', 'sesion_id'])
            ->orderBy('id')
            ->chunkById(500, function ($proyectos) use ($ahora) {
                $filas = $proyectos->map(fn ($proyecto) => [
                    'sesion_id' => $proyecto->sesion_id,
                    'proyecto_id' => $proyecto->id,
                    'created_at' => $ahora,
                    'updated_at' => $ahora,
                ])->all();

                DB::table('sesion_proyecto')->insertOrIgnore($filas);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesion_proyecto');
    }
};
