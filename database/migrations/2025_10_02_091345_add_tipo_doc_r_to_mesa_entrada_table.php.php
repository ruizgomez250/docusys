<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mesa_entrada', function (Blueprint $table) {
            // Creamos la nueva columna después de id_tipo_doc
            $table->unsignedBigInteger('id_tipo_docr')->nullable()->after('id_tipo_doc');
        });

        // Copiamos los datos desde id_tipo_doc a id_tipo_doc_r
        DB::statement('UPDATE mesa_entrada SET id_tipo_docr = id_tipo_doc');
    }

    public function down(): void
    {
        Schema::table('mesa_entrada', function (Blueprint $table) {
            $table->dropColumn('id_tipo_docr');
        });
    }
};
