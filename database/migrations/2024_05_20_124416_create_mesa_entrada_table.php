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
        Schema::create('mesa_entrada', function (Blueprint $table) {
            $table->id();
            $table->string('nro_mentrada');
            $table->integer('anho');
            $table->date('fecha_recepcion');
            $table->foreignId('id_origen')->constrained('origen');
            $table->foreignId('id_tipo_doc')->constrained('tipo_docs');
            $table->foreignId('id_destino')->constrained('destinos');
            $table->text('observacion')->nullable();
            $table->boolean('estado')->default(1);
            $table->foreignId('id_usuario')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mesa_entrada');
    }
};
