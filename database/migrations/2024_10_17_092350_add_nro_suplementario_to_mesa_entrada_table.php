<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('mesa_entrada', function (Blueprint $table) {
            $table->string('nro_suplementario')->nullable()->after('nro_mentrada');
        });
    }

    public function down()
    {
        Schema::table('mesa_entrada', function (Blueprint $table) {
            $table->dropColumn('nro_suplementario');
        });
    }
};
