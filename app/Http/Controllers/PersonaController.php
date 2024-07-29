<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use Illuminate\Http\Request;

class PersonaController extends Controller
{
    public function index()
    {
        $personas = Persona::all();
        return view('personas.index', compact('personas'));
    }

    public function create()
    {
        return view('personas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ci' => 'nullable|string|unique:personas',
            'nombre' => 'required|string',
            'apellido' => 'required|string',
            'direccion' => 'nullable|string',
            'telefono' => 'nullable|string',
            'email' => 'nullable|email|unique:personas',
            'fecha_nac' => 'nullable|date',
        ]);

        Persona::create($request->all());

        return redirect()->route('personas.index')->with('success', 'Persona creada exitosamente.');
    }

    public function show(Persona $persona)
    {
        return view('personas.show', compact('persona'));
    }

    public function edit(Persona $persona)
    {
        return view('personas.edit', compact('persona'));
    }

    public function update(Request $request, Persona $persona)
    {
        $request->validate([
            'ci' => 'nullable|string|unique:personas,ci,' . $persona->id,
            'nombre' => 'required|string',
            'apellido' => 'required|string',
            'direccion' => 'nullable|string',
            'telefono' => 'nullable|string',
            'email' => 'nullable|email|unique:personas,email,' . $persona->id,
            'fecha_nac' => 'nullable|date',
        ]);

        $persona->update($request->all());

        return redirect()->route('personas.index')->with('success', 'Persona actualizada exitosamente.');
    }

    public function destroy(Persona $persona)
    {
        $persona->delete();
        return redirect()->route('personas.index')->with('success', 'Persona eliminada exitosamente.');
    }
}
