<?php

namespace App\Http\Controllers;


use App\Models\Legislador;
use Illuminate\Http\Request;
use App\Models\PartidoPolitico;
use App\Models\PeriodoLegislativo;
use Illuminate\Support\Facades\DB;

class LegisladorController extends Controller
{
    public function index()
    {
        $legislador = Legislador::all();
        return view('legislador.index', compact('legislador'));
    }

    public function create()
    {
        // Ordenar en orden descendente por nombre
        $partidos = PartidoPolitico::orderBy('nombre', 'asc')->get();
        $periodos = PeriodoLegislativo::orderBy('nombre', 'desc')->get();


        return view('legislador.create', compact('partidos', 'periodos'));
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            //    'ci' => 'unique:legisladores,ci',
            'nombre' => 'required',
            'apellido' => 'required',
            'apodo' => 'nullable',
            'circunscripcion' => 'nullable',
            'telefono' => 'nullable',
            'email' => 'nullable|email|unique:legisladores,email',
            'fecha_nac' => 'nullable|date',

            'partido_id' => 'required|exists:partidos_politicos,id',
            'periodos' => 'required|array',
            'periodos.*' => 'exists:periodos_legislativos,id',
        ]);

        DB::beginTransaction();

        try {

            $legislador = Legislador::create($validated);

            //  dd($request->periodos);

            //dd($legislador->id);
            // Asociar el legislador con los periodos legislativos
            //    $legislador->periodos()->attach($request->periodos);
            $legislador->periodos()->sync($request->periodos);

            DB::commit();

            return redirect()->route('legislador.index')->with('success', 'Legislador creados exitosamente.');
        } catch (\Exception $e) {
            //  DB::rollBack();
            return back()->with('error', 'Error al crear Legislador: ' . $e->getMessage());
        }
    }

    public function show(Legislador $legislador)
    {
        return view('legislador.show', compact('legislador'));
    }

    public function edit(Legislador $legislador)
    {
        // Cargar la relación 'periodos' para el legislador
        $legislador->load('periodos');
        // Verificar que los periodos están cargados
        // Se puede utilizar 'dd' para depurar si es necesario
        // dd($legislador->periodos);

        // Obtener todos los periodos legislativos disponibles


        // Obtener todos los periodos legislativos disponibles
        $periodo = PeriodoLegislativo::all();
        // Obtener solo los periodos legislativos asociados al legislador
        $periodotwo = $legislador->periodos;
        return view('legislador.edit', compact('legislador', 'periodo'));
    }

    public function update(Request $request, Legislador $legislador)
    {
      //  dd($request);
        $request->validate([
            'nombre' => 'required|string',
            'apellido' => 'required|string',
            'apodo' => 'nullable|string',
            'circunscripcion' => 'nullable|string',
            'telefono' => 'nullable|string',            
            'email' => 'nullable|email|unique:legisladores,email,' . $legislador->id,
            'fecha_nac' => 'nullable|date',
            'periodos' => 'nullable|array',
            'periodos.*' => 'exists:periodos_legislativos,id',
        ]);

        // Actualizar los datos del legislador
        $legislador->update($request->except('periodos'));
        // dd($request->input('periodos', []));
        // Sincronizar los periodos legislativos, si se proporcionan
        if ($request->has('periodos')) {
            $legislador->periodos()->sync($request->input('periodos', []));
        } else {
            // Si no se proporcionan periodos, desvincular todos los periodos
            $legislador->periodos()->sync([]);
        }

        return redirect()->route('legislador.index')->with('success', 'Legislador actualizado exitosamente.');
    }



    public function destroy(Legislador $legislador)
    {
        $legislador->delete();
        return redirect()->route('legislador.index')->with('success', 'Legislador eliminada exitosamente.');
    }
}
