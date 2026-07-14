<?php

namespace App\Http\Controllers;

use App\Models\Entidad;
use Illuminate\Http\Request;

class EntidadController extends Controller
{
    public function index()
    {
        $entidades = Entidad::all();
        return view('entidades.index', compact('entidades'));
    }

    public function create()
    {
        return view('entidades.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        Entidad::create($request->all());

        return redirect()->route('entidades.index')->with('success', 'Entidad creada exitosamente.');
    }

    public function show(Entidad $entidad)
    {
        return view('entidades.show', compact('entidad'));
    }

    public function edit(Entidad $entidade)
    {
        $entidad = $entidade;
        return view('entidades.edit', compact('entidad'));
    }

    public function update(Request $request, Entidad $entidade)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        $entidade->update($request->all());

        return redirect()->route('entidades.index')->with('success', 'Entidad actualizada exitosamente.');
    }

    public function destroy(Entidad $entidad)
    {
        $entidad->delete();

        return redirect()->route('entidades.index')->with('success', 'Entidad eliminada exitosamente.');
    }
}
