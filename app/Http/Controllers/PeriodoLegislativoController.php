<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PeriodoLegislativo;

class PeriodoLegislativoController extends Controller
{
    // Lista todos los periodos legislativos
    public function index()
    {
        $periodos = PeriodoLegislativo::all();
        return view('periodos.index', compact('periodos'));
    }

    // Muestra el formulario de creación
    public function create()
    {
        return view('periodos.create');
    }

    // Almacena un nuevo periodo legislativo
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'inicio' => 'required|date',
            'fin' => 'required|date',
            'activo' => 'boolean'
        ]);

        PeriodoLegislativo::create($request->all());
        return redirect()->route('periodos.index')->with('success', 'Periodo legislativo creado exitosamente.');
    }

    // Muestra el formulario de edición
    public function edit($id)
    {
        $periodo = PeriodoLegislativo::findOrFail($id);
        return view('periodos.edit', compact('periodo'));
    }

    // Actualiza un periodo legislativo
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'inicio' => 'required|date',
            'fin' => 'required|date',
            'activo' => 'boolean'
        ]);

        $periodo = PeriodoLegislativo::findOrFail($id);
        $periodo->update($request->all());
        return redirect()->route('periodos.index')->with('success', 'Periodo legislativo actualizado exitosamente.');
    }

    // Elimina un periodo legislativo
    public function destroy($id)
    {
        $periodo = PeriodoLegislativo::findOrFail($id);
        $periodo->delete();
        return redirect()->route('periodos.index')->with('success', 'Periodo legislativo eliminado exitosamente.');
    }
}
