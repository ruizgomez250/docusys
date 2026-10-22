<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Destinos oficiales (estados de trámite y comisiones de la Cámara de Diputados).
     * El orden define el orden de aparición en el formulario.
     */
    private const DESTINOS = [
        'A SUS ANTECEDENTES',
        'SE TOMA NOTA',
        'A DISPOSICIÓN DE LOS SEÑORES DIPUTADOS',
        'A CONSIDERACIÓN DEL PLENARIO',
        'A COMISIONES',
        'Asuntos Constitucionales',
        'Asuntos Económicos y Financieros',
        'Legislación y Codificación',
        'Relaciones Exteriores',
        'Justicia, Trabajo y Previsión Social',
        'Derechos Humanos',
        'Educación, Cultura y Culto',
        'Obras, Servicios Públicos y Comunicaciones',
        'Prensa, Comunicación Social, Artes y Espectáculos',
        'Agricultura y Ganadería',
        'Defensa Nacional, Seguridad, Inteligencia y Orden Interno',
        'Industria, Comercio, Turismo y Cooperativismo',
        'Salud Pública',
        'Asuntos Municipales y Departamentales',
        'Desarrollo Social, Población y Vivienda',
        'Presupuesto',
        'Cuentas y Control de Ejecución Presupuestaria',
        'Peticiones, Poderes, Reglamento y Redacción',
        'Lucha contra el Narcotráfico, Ilícitos Conexos, Graves y de Prevención de las Adicciones',
        'Ciencia y Tecnología',
        'Energía y Minería',
        'Ambiente, Desarrollo Sostenible y Cambio Climático',
        'Bienestar Rural',
        'Equidad Social e Igualdad de Derechos del Hombre y la Mujer',
        'Deportes',
        'Pueblos Indígenas',
        'Entes Binacionales Hidroeléctricos',
        'Asuntos Migratorios y Desarrollo',
        'Juventud y Bono Demográfico',
        'Familia y Personas Adultas Mayores',
        'Defensa de los Derechos de las Personas con Discapacidad',
        'Niñez y Adolescencia',
        'Control y Seguimiento de Inversión de Empréstitos, Donaciones y Emisión de Bonos',
        'Asuntos Electorales',
        'Reestructuración y Modernización del Estado',
        'Protección y Bienestar Animal',
    ];

    public function up(): void
    {
        Schema::table('proyectos_destinos', function (Blueprint $table) {
            $table->integer('orden')->nullable()->after('nombre');
        });

        DB::table('proyectos_destinos')->delete();

        $ahora = now();
        $filas = [];

        foreach (self::DESTINOS as $indice => $nombre) {
            $filas[] = [
                'nombre' => $nombre,
                'orden' => $indice + 1,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ];
        }

        DB::table('proyectos_destinos')->insert($filas);

        // Normaliza los destinos ya guardados en los proyectos: los que coinciden
        // (sin importar mayúsculas) se actualizan al nombre oficial y el resto se vacía.
        $oficiales = collect(self::DESTINOS)->mapWithKeys(fn ($nombre) => [mb_strtolower($nombre) => $nombre]);

        DB::table('proyectos_en_estudio')
            ->whereNotNull('destino')
            ->where('destino', '!=', '')
            ->orderBy('id')
            ->each(function ($proyecto) use ($oficiales) {
                $clave = mb_strtolower(trim($proyecto->destino));

                if ($oficiales->has($clave)) {
                    if ($oficiales[$clave] !== $proyecto->destino) {
                        DB::table('proyectos_en_estudio')
                            ->where('id', $proyecto->id)
                            ->update(['destino' => $oficiales[$clave]]);
                    }
                } else {
                    DB::table('proyectos_en_estudio')
                        ->where('id', $proyecto->id)
                        ->update(['destino' => null]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('proyectos_destinos', function (Blueprint $table) {
            $table->dropColumn('orden');
        });
    }
};
