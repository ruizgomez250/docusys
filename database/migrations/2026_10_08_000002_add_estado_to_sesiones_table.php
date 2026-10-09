<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sesiones', function (Blueprint $table) {
            $table->string('estado', 20)->default('BORRADOR')->after('nro_sesion');
            $table->timestamp('finalizado_at')->nullable()->after('estado');
        });
    }

    public function down(): void
    {
        Schema::table('sesiones', function (Blueprint $table) {
            $table->dropColumn(['estado', 'finalizado_at']);
        });
    }
};
