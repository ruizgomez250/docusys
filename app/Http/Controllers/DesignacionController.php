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

    return view('designacion.index', compact('designaciones', 'legislador'));
}

    public function create()
    {
        $legisladores = Legislador::all();
        $entidades = Entidad::all();
        $cargos = Cargo::all();
        return view('designacion.create', compact('legisladores', 'entidades', 'cargos'));
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

        return redirect()->route('designacion.index')->with('success', 'Designación creada con éxito.');
    }

    public function edit(Designacion $designacion)
    {
        $legisladores = Legislador::all();
        $entidades = Entidad::all();
        $cargos = Cargo::all();
        return view('designacion.edit', compact('designacion', 'legisladores', 'entidades', 'cargos'));
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

        return redirect()->route('designacion.index')->with('success', 'Designación actualizada con éxito.');
    }

    public function destroy(Designacion $designacion)
    {
        $designacion->delete();

        return redirect()->route('designacion.index')->with('success', 'Designación eliminada con éxito.');
    }
}
