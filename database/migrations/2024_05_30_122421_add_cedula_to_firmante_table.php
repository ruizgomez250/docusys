<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('firmantes', function (Blueprint $table) {
            $table->string('cedula')->after('telefono');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('firmantes', function (Blueprint $table) {
            $table->dropColumn('cedula');
        });
    }
};
