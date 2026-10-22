<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proyectos_en_estudio', function (Blueprint $table) {
            $table->boolean('edicion_habilitada')->default(false)->after('sesion_id');
            $table->timestamp('edicion_habilitada_at')->nullable()->after('edicion_habilitada');
            $table->foreignId('edicion_habilitada_por')->nullable()->after('edicion_habilitada_at')
                ->constrained('users')->nullOnDelete();
        });

        DB::table('permissions')->insertOrIgnore([
            'name' => 'Desbloquear Proyectos Finalizados',
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::table('proyectos_en_estudio', function (Blueprint $table) {
            $table->dropForeign(['edicion_habilitada_por']);
            $table->dropColumn([
                'edicion_habilitada',
                'edicion_habilitada_at',
                'edicion_habilitada_por',
            ]);
        });

        DB::table('permissions')
            ->where('name', 'Desbloquear Proyectos Finalizados')
            ->where('guard_name', 'web')
            ->delete();
    }
};
