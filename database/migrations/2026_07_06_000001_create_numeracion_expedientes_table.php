<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('numeracion_expedientes', function (Blueprint $table) {
            $table->id();
            $table->integer('nro_expediente');
            $table->year('anho');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('numeracion_expedientes');
    }
};
