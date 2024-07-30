<?php

namespace App\Http\Controllers;

use App\Models\PartidoPolitico;
use Illuminate\Http\Request;

class PartidoPoliticoController extends Controller
{
    public function index()
    {
        $partidos = PartidoPolitico::all();
        return view('partidospoliticos.index', compact('partidos'));
    }

    public function create()
    {
        return view('partidospoliticos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|unique:partidos_politicos|max:255',
            'sigla' => 'nullable|unique:partidos_politicos|max:10',
            'descripcion' => 'nullable|string',
        ]);

        PartidoPolitico::create($validated);

        return redirect()->route('partidos.index')->with('success', 'Partido político creado con éxito.');
    }

    public function show(PartidoPolitico $partido)
    {
        return view('partidospoliticos.show', compact('partido'));
    }

    public function edit(PartidoPolitico $partido)
    {
        return view('partidospoliticos.edit', compact('partido'));
    }

    public function update(Request $request, PartidoPolitico $partido)
    {
        $validated = $request->validate([
            'nombre' => 'required|unique:partido_politicos,nombre,' . $partido->id,
            'sigla' => 'nullable|unique:partido_politicos,sigla,' . $partido->id,
            'descripcion' => 'nullable|string',
        ]);

        $partido->update($validated);

        return redirect()->route('partidos.index')->with('success', 'Partido político actualizado con éxito.');
    }

    public function destroy(PartidoPolitico $partido)
    {
        $partido->delete();

        return redirect()->route('partidos.index')->with('success', 'Partido político eliminado con éxito.');
    }
}
