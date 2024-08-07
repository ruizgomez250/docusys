<?php



namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Legislador;
use App\Models\RegistroAsistencia;
use Carbon\Carbon;

class RegistroAsistenciaController extends Controller
{
    // Método para mostrar el formulario de asistencia
    public function create(Request $request)
    {
        $fechaSesion = $request->input('fecha_sesion', Carbon::now()->format('Y-m-d'));

        // Obtén los legisladores activos y sus permisos, si los hay
        $legisladores = Legislador::where('activo', 1)
            ->orderBy('nombre', 'asc')
            ->with(['permiso' => function ($query) use ($fechaSesion) {
                // Filtra permisos por fecha de sesión y estado activo
                $query->where('fecha', $fechaSesion)
                      ->where('estado', 'Vigente'); // Cambia 'Vigente' por el valor adecuado si es diferente
            }])
            ->get();


        $asistencias = RegistroAsistencia::where('fecha_sesion', $fechaSesion)
            ->get()
            ->keyBy('legislador_id');

        return view('asistencia.create', compact('legisladores', 'fechaSesion', 'asistencias'));
    }

    public function store(Request $request)
    {
        $fechaSesion = $request->input('fecha_sesion');
        $legisladorIds = $request->input('legisladores', []);
        $estados = $request->input('estado', []);
        $justificaciones = $request->input('justificacion', []);

        // Limpiar registros existentes para esa fecha
        RegistroAsistencia::where('fecha_sesion', $fechaSesion)->delete();

        foreach ($legisladorIds as $index => $legisladorId) {
            // Usa el índice para obtener los valores de estado y justificación
            $estado = isset($estados[$index]) ? $estados[$index] : 'Ausente';
            $justificacion = isset($justificaciones[$index]) ? $justificaciones[$index] : null;

            RegistroAsistencia::updateOrCreate(
                [
                    'legislador_id' => $legisladorId,
                    'fecha_sesion' => $fechaSesion
                ],
                [
                    'estado' => $estado,
                    'justificacion' => $justificacion
                ]
            );
        }

        return redirect()->route('registro_asistencias.create', ['fecha_sesion' => $fechaSesion])
            ->with('status', 'Asistencia registrada exitosamente');
    }
}
