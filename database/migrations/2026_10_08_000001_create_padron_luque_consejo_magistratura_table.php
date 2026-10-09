<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('padron_luque_consejo_magistratura', function (Blueprint $table) {
            $table->id();
            $table->string('apellido');
            $table->string('nombre');
            $table->string('cedula', 20)->unique();
            $table->string('matricula', 20);
            $table->string('codigo_departamento', 10)->nullable();
            $table->string('departamento', 100)->nullable();
            $table->string('codigo_distrito', 10)->nullable();
            $table->string('distrito', 100)->nullable();
            $table->string('seccion', 20)->nullable();
            $table->string('telefono', 30)->nullable();
            $table->text('universidad')->nullable();
            $table->unsignedSmallInteger('egreso')->nullable();
            $table->unsignedSmallInteger('pagina_origen');
            $table->unsignedSmallInteger('fila_origen');
            $table->timestamps();
        });

        $archivo = database_path('data/padron_luque_consejo_magistratura.csv');
        $manejador = fopen($archivo, 'rb');

        if ($manejador === false) {
            throw new RuntimeException("No se pudo abrir {$archivo}");
        }

        $encabezados = fgetcsv($manejador);
        $encabezados[0] = preg_replace('/^\xEF\xBB\xBF/', '', $encabezados[0]);
        $lote = [];
        $ahora = now();

        while (($valores = fgetcsv($manejador)) !== false) {
            $fila = array_combine($encabezados, $valores);
            $nuloSiVacio = static fn ($valor) => $valor === '' ? null : $valor;

            $lote[] = [
                'apellido' => $fila['apellido'],
                'nombre' => $fila['nombre'],
                'cedula' => $fila['cedula'],
                'matricula' => $fila['matricula'],
                'codigo_departamento' => $nuloSiVacio($fila['codigo_departamento']),
                'departamento' => $nuloSiVacio($fila['departamento']),
                'codigo_distrito' => $nuloSiVacio($fila['codigo_distrito']),
                'distrito' => $nuloSiVacio($fila['distrito']),
                'seccion' => $nuloSiVacio($fila['seccion']),
                'telefono' => $nuloSiVacio($fila['telefono']),
                'universidad' => $nuloSiVacio($fila['universidad']),
                'egreso' => $nuloSiVacio($fila['egreso']),
                'pagina_origen' => $fila['pagina_origen'],
                'fila_origen' => $fila['fila_origen'],
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ];

            if (count($lote) === 500) {
                DB::table('padron_luque_consejo_magistratura')->insert($lote);
                $lote = [];
            }
        }

        fclose($manejador);

        if ($lote !== []) {
            DB::table('padron_luque_consejo_magistratura')->insert($lote);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('padron_luque_consejo_magistratura');
    }
};
