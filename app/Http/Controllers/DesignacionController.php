<?php

namespace App\Http\Controllers;

use App\Models\Designacion;
use App\Models\Legislador;
use App\Models\Entidad;
use App\Models\Cargo;
use Illuminate\Http\Request;

class DesignacionController extends Controller
{
// Mostrar todas las designaciones de un legislador específico
public function index($legisladorId)
{
    $legislador = Legislador::findOrFail($legisladorId);
    $designaciones = Designacion::where('legislador_id', $legisladorId)
                                ->with(['entidad', 'cargo'])
                                ->get();
                                
    return view('designaciones.index', compact('designaciones', 'legislador'));
}

    public function create($legisladorId)
    {
           // Buscar el legislador por su ID y lanzar una excepción si no se encuentra
           $legislador = Legislador::findOrFail($legisladorId);
        $entidades = Entidad::all();
        $cargos = Cargo::all();
        return view('designaciones.create', compact('legislador', 'entidades', 'cargos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'legislador_id' => 'required|exists:legisladores,id',
            'entidad_id' => 'required|exists:entidades,id',
            'cargo_id' => 'nullable|exists:cargos,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        ]);
        Designacion::create($request->all());
        return redirect()->route('designacion.inicio',$request->legislador_id)->with('success', 'Designación creada con éxito.');
    }

    public function edit(Designacion $designacion)
    {
        $legisladores = Legislador::all();
        $entidades = Entidad::all();
        $cargos = Cargo::all();
        return view('designaciones.edit', compact('designacion', 'legisladores', 'entidades', 'cargos'));
    }

    public function update(Request $request, Designacion $designacion)
    {
        $request->validate([
            'legislador_id' => 'required|exists:legisladores,id',
            'entidad_id' => 'required|exists:entidades,id',
            'cargo_id' => 'nullable|exists:cargos,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        ]);

        $designacion->update($request->all());

        return redirect()->route('designaciones.index')->with('success', 'Designación actualizada con éxito.');
    }

    public function destroy(Designacion $designacione)
    {
       // dd($designacione);
        $designacione->delete();

        return redirect()->route('designacion.inicio',$designacione->legislador_id)->with('success', 'Designación eliminada con éxito.');
    }
}
