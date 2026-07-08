<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('proyectos_configuracion', function (Blueprint $table) {
            $table->id();
            $table->string('membrete')->nullable();
            $table->text('leyenda')->nullable();
            $table->timestamps();
        });

        DB::table('proyectos_configuracion')->insert([
            'membrete' => null,
            'leyenda' => '',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('proyectos_configuracion');
    }
};
