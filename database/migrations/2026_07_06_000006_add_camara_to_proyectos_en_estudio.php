<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('proyectos_en_estudio', function (Blueprint $table) {
            $table->string('camara')->nullable()->after('anho');
        });
    }

    public function down()
    {
        Schema::table('proyectos_en_estudio', function (Blueprint $table) {
            $table->dropColumn('camara');
        });
    }
};
