<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('archivos_documentos', function (Blueprint $table) {
            $table->unsignedBigInteger('id_recorrido')->nullable()->after('id_usuario'); // Puedes establecerla como nullable si no todos los registros tienen un recorrido asociado
            $table->foreign('id_recorrido')->references('id')->on('recorrido_doc')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('archivos_documentos', function (Blueprint $table) {
            $table->dropForeign(['id_recorrido']);
            $table->dropColumn('id_recorrido');
        });
    }
};
