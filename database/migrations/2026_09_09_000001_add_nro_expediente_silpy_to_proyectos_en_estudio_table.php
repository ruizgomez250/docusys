<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        Schema::table('proyectos_en_estudio', function (Blueprint $table) {
            $table->string('nro_expediente_silpy', 20)->nullable()->after('nro_expediente');
        });
        DB::table('proyectos_en_estudio')->where('nro_expediente', '>', 0)
            ->update(['nro_expediente_silpy' => DB::raw('nro_expediente')]);
    }
    public function down() {
        Schema::table('proyectos_en_estudio', function (Blueprint $table) {
            $table->dropColumn('nro_expediente_silpy');
        });
    }
};
