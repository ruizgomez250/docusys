<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('destinos', function (Blueprint $table) {
            $table->integer('orden')->default(0)->after('default'); // Agrega la columna después de 'default'
        });
    }

    public function down(): void {
        Schema::table('destinos', function (Blueprint $table) {
            $table->dropColumn('orden'); // Elimina la columna si se revierte la migración
        });
    }
};
