<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proyectos_en_estudio', function (Blueprint $table) {
            $table->boolean('usar_documento_padre')->default(false)->after('sesion_id');
            $table->foreignId('documento_padre_id')->nullable()->constrained('proyectos_en_estudio')->onDelete('set null')->after('usar_documento_padre');
        });
    }

    public function down(): void
    {
        Schema::table('proyectos_en_estudio', function (Blueprint $table) {
            $table->dropForeign(['documento_padre_id']);
            $table->dropColumn(['documento_padre_id', 'usar_documento_padre']);
        });
    }
};
