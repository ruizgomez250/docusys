<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('firmantes', function (Blueprint $table) {
            $table->integer('origen')->nullable(); // Si deseas un tipo entero
            // $table->decimal('origen', 8, 2)->nullable(); // Si prefieres un tipo decimal
        });
    }

    public function down()
    {
        Schema::table('firmantes', function (Blueprint $table) {
            $table->dropColumn('origen');
        });
    }
};
