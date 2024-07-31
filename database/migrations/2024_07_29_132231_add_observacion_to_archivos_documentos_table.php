<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('archivos_documentos', function (Blueprint $table) {
            $table->string('observacion')->nullable()->after('ruta_archivo');
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
            $table->dropColumn('observacion');
        });
    }
};
