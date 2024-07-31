<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('archivos_documentos', function (Blueprint $table) {
            $table->string('link', 500)->nullable()->after('observacion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('archivos_documentos', function (Blueprint $table) {
            $table->dropColumn('link');
        });
    }
};
