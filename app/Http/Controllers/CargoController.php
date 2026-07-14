<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use Illuminate\Http\Request;

class CargoController extends Controller
{
    public function index()
    {
        $cargos = Cargo::all();
        return view('cargos.index', compact('cargos'));
    }

    public function create()
    {
        return view('cargos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        Cargo::create($request->all());

        return redirect()->route('cargos.index')->with('success', 'Cargo creado exitosamente.');
    }

    public function show(Cargo $cargo)
    {
        return view('cargos.show', compact('cargo'));
    }

    public function edit(Cargo $cargo)
    {
        return view('cargos.edit', compact('cargo'));
    }

    public function update(Request $request, Cargo $cargo)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        $cargo->update($request->all());

        return redirect()->route('cargos.index')->with('success', 'Cargo actualizado exitosamente.');
    }

    public function destroy(Cargo $cargo)
    {
        $cargo->delete();

        return redirect()->route('cargos.index')->with('success', 'Cargo eliminado exitosamente.');
    }
}
